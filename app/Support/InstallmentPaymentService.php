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

    public static function markInstallmentPaid(Payment $payment, RequestPayment $installment): void
    {
        $payment->update(['status' => 'completed']);

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
