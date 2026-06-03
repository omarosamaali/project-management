<?php

namespace App\Support;

use Carbon\Carbon;

class WorkTimeMoment
{
    /**
     * Y-m-d من حقل date (قد يكون Carbon أو نصاً بصيغة datetime).
     */
    public static function dateKey(mixed $date): string
    {
        if ($date instanceof Carbon) {
            return $date->format('Y-m-d');
        }

        $raw = trim((string) $date);
        if ($raw === '') {
            return Carbon::today()->format('Y-m-d');
        }

        return Carbon::parse($raw)->format('Y-m-d');
    }

    /**
     * لحظة تسجيل حضور/انصراف دون تكرار جزء الوقت في السلسلة.
     */
    public static function at(mixed $date, mixed $time): Carbon
    {
        $timeStr = trim((string) $time);

        if ($timeStr !== '' && preg_match('/^\d{4}-\d{2}-\d{2}/', $timeStr)) {
            return Carbon::parse($timeStr);
        }

        $dateStr = self::dateKey($date);

        if ($timeStr === '') {
            return Carbon::parse($dateStr)->startOfDay();
        }

        return Carbon::parse($dateStr . ' ' . $timeStr);
    }
}
