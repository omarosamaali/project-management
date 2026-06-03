<?php

namespace App\Support;

use App\Models\Payment;
use App\Models\RequestPayment;
use App\Services\ZiinaSystemPaymentHandler;
use Illuminate\Http\Request;

class InstallmentPaymentService
{
    public static function paidStatuses(): array
    {
        return ['completed', 'paid', 'succeeded'];
    }

    public static function isZiinaPaid(?array $paymentIntent): bool
    {
        return in_array($paymentIntent['status'] ?? '', self::paidStatuses(), true);
    }

    public static function resolveInstallmentId(Request $request): ?int
    {
        $id = $request->query('installment_id') ?? $request->query('installment');

        return $id ? (int) $id : null;
    }

    public static function resolvePaymentIntentId(Request $request, ?RequestPayment $installment = null): ?string
    {
        $intentId = $request->query('payment_intent_id');
        if ($intentId) {
            return $intentId;
        }

        if (!$installment) {
            return null;
        }

        return Payment::query()
            ->where('request_payment_id', $installment->id)
            ->whereNotNull('payment_id')
            ->orderByDesc('id')
            ->value('payment_id');
    }

    public static function isPaidStatus(?string $status): bool
    {
        return in_array($status ?? '', self::paidStatuses(), true);
    }

    /**
     * تأكيد الدفع من webhook/callback (حالات Ziina: completed, paid, succeeded).
     */
    public static function confirmPaymentRecord(Payment $payment, ?string $status = null): bool
    {
        if ($status !== null && !self::isPaidStatus($status)) {
            return false;
        }

        if ($payment->request_payment_id) {
            $installment = RequestPayment::find($payment->request_payment_id);
            if ($installment && $installment->status !== 'paid') {
                self::markInstallmentPaid($payment, $installment);

                return true;
            }

            return $installment?->status === 'paid';
        }

        if ($payment->special_request_id) {
            $payment->update(['status' => 'completed']);
            $payment->specialRequest?->update(['status' => 'in_progress']);

            return true;
        }

        return false;
    }

    public static function findZiinaPaymentForInstallment(RequestPayment $installment): ?Payment
    {
        $linked = Payment::query()
            ->where('request_payment_id', $installment->id)
            ->whereIn('status', ['completed', 'paid'])
            ->latest('id')
            ->first();

        if ($linked) {
            return $linked;
        }

        return Payment::query()
            ->where('special_request_id', $installment->special_request_id)
            ->whereIn('status', ['completed', 'paid'])
            ->where(function ($query) use ($installment) {
                $query->where('request_payment_id', $installment->id)
                    ->orWhere(function ($q) use ($installment) {
                        $q->whereNull('request_payment_id')
                            ->where('original_price', $installment->amount);
                    });
            })
            ->latest('id')
            ->first();
    }

    /**
     * بيانات فاتورة عند التأكيد اليدوي أو غياب سجل payments.
     */
    /**
     * @return array{base: float, fees: float, total: float}
     */
    public static function invoiceAmounts(?Payment $payment, ?RequestPayment $installment = null): array
    {
        if ($installment) {
            $base = (float) $installment->amount;
        } elseif ($payment?->original_price) {
            $base = (float) $payment->original_price;
        } else {
            $paidTotal = (float) ($payment?->amount ?? 0);
            $base = $paidTotal > 0 ? round(($paidTotal - 2) / 1.079, 2) : 0.0;
        }

        if ($payment && (float) $payment->fees > 0) {
            $fees = (float) $payment->fees;
        } elseif ($payment && (float) $payment->amount > 0 && (float) $payment->original_price > 0) {
            $fees = round((float) $payment->amount - (float) $payment->original_price, 2);
        } else {
            $fees = round(($base * 0.079) + 2, 2);
        }

        if ($payment && (float) $payment->amount > 0) {
            $total = (float) $payment->amount;
        } else {
            $total = round($base + $fees, 2);
        }

        return [
            'base' => $base,
            'fees' => $fees,
            'total' => $total,
        ];
    }

    public static function buildInvoicePaymentPreview(RequestPayment $installment): Payment
    {
        $base = (float) $installment->amount;
        $fees = round(($base * 0.079) + 2, 2);

        $payment = new Payment([
            'user_id' => $installment->specialRequest?->user_id,
            'special_request_id' => $installment->special_request_id,
            'request_payment_id' => $installment->id,
            'original_price' => $base,
            'fees' => $fees,
            'amount' => $base + $fees,
            'status' => 'completed',
            'payment_method' => 'manual',
            'currency' => 'AED',
        ]);
        $payment->created_at = $installment->paid_at ?? now();

        return $payment;
    }

    public static function markInstallmentPaid(Payment $payment, RequestPayment $installment): void
    {
        $payment->update([
            'status' => 'completed',
            'request_payment_id' => $payment->request_payment_id ?? $installment->id,
            'special_request_id' => $payment->special_request_id ?? $installment->special_request_id,
        ]);

        $installment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $installment->specialRequest?->refreshPaymentStatus();
    }

    /**
     * إرجاع الدفعة إلى غير مدفوعة إذا لم يُؤكَّد الدفع في Ziina.
     */
    public static function revertInstallmentIfNotPaid(
        RequestPayment $installment,
        string $paymentIntentId,
        ZiinaSystemPaymentHandler $handler
    ): void {
        if ($installment->status !== 'pending') {
            return;
        }

        try {
            $intent = $handler->getPaymentIntent($paymentIntentId);
            if (!self::isZiinaPaid($intent)) {
                $installment->update(['status' => 'unpaid']);
                Payment::where('payment_id', $paymentIntentId)
                    ->where('request_payment_id', $installment->id)
                    ->update(['status' => 'failed']);
            }
        } catch (\Throwable) {
            $installment->update(['status' => 'unpaid']);
        }
    }
}
