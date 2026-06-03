<?php

namespace App\Support;

class DurationFormatter
{
    public static function format(int $totalSeconds): string
    {
        $totalSeconds = max(0, $totalSeconds);
        $hours   = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);
        $seconds = $totalSeconds % 60;

        return sprintf('%d ساعة %d دقيقة %d ثانية', $hours, $minutes, $seconds);
    }
}
