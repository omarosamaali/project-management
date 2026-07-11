<?php

namespace App\Support;

use App\Models\RequestPayment;
use Illuminate\Support\Collection;

class RequestPaymentSync
{
    /**
     * مزامنة دفعات المشروع دون المساس بالدفعات المدفوعة أو قيد التأكيد.
     *
     * @param  array<int, array{id?: int, name?: string, amount?: mixed, due_date?: string|null}>  $installments
     * @return array<int>
     */
    public static function syncForSpecialRequest(int $specialRequestId, string $paymentType, float $price, ?array $installments): array
    {
        $existing = RequestPayment::where('special_request_id', $specialRequestId)->get();
        $keptIds = [];

        if ($paymentType === 'single') {
            return self::syncSinglePayment($specialRequestId, $price, $existing);
        }

        foreach ($installments ?? [] as $installment) {
            $name = trim((string) ($installment['name'] ?? ''));
            if ($name === '' || ! isset($installment['amount'])) {
                continue;
            }

            $id = isset($installment['id']) ? (int) $installment['id'] : null;
            $record = $id ? $existing->firstWhere('id', $id) : null;

            if ($record) {
                $keptIds[] = $record->id;

                if (! self::isLocked($record)) {
                    $record->update([
                        'payment_name' => $name,
                        'amount' => $installment['amount'],
                        'due_date' => $installment['due_date'] ?? null,
                    ]);
                }

                continue;
            }

            $created = RequestPayment::create([
                'special_request_id' => $specialRequestId,
                'payment_name' => $name,
                'amount' => $installment['amount'],
                'due_date' => $installment['due_date'] ?? null,
                'status' => 'unpaid',
            ]);

            $keptIds[] = $created->id;
        }

        RequestPayment::where('special_request_id', $specialRequestId)
            ->whereNotIn('id', $keptIds)
            ->whereNotIn('status', ['paid', 'pending'])
            ->delete();

        return $keptIds;
    }

    /**
     * @param  Collection<int, RequestPayment>  $existing
     * @return array<int>
     */
    private static function syncSinglePayment(int $specialRequestId, float $price, Collection $existing): array
    {
        $locked = $existing->first(fn (RequestPayment $payment) => self::isLocked($payment));

        if ($locked) {
            RequestPayment::where('special_request_id', $specialRequestId)
                ->where('id', '!=', $locked->id)
                ->whereNotIn('status', ['paid', 'pending'])
                ->delete();

            return [$locked->id];
        }

        $unpaid = $existing->firstWhere('status', 'unpaid');

        if ($unpaid) {
            $unpaid->update([
                'payment_name' => 'الدفعة الكاملة',
                'amount' => $price,
            ]);

            RequestPayment::where('special_request_id', $specialRequestId)
                ->where('id', '!=', $unpaid->id)
                ->whereNotIn('status', ['paid', 'pending'])
                ->delete();

            return [$unpaid->id];
        }

        RequestPayment::where('special_request_id', $specialRequestId)
            ->whereNotIn('status', ['paid', 'pending'])
            ->delete();

        $created = RequestPayment::create([
            'special_request_id' => $specialRequestId,
            'payment_name' => 'الدفعة الكاملة',
            'amount' => $price,
            'status' => 'unpaid',
        ]);

        return [$created->id];
    }

    private static function isLocked(RequestPayment $payment): bool
    {
        return in_array($payment->status, ['paid', 'pending'], true);
    }
}
