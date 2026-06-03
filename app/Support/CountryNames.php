<?php

namespace App\Support;

use Carbon\Carbon;

class CountryNames
{
    private const CACHE_KEY = 'country_names_ar_v2';

    /**
     * @return array<string, string>
     */
    public static function arabicNames(): array
    {
        return cache()->remember(self::CACHE_KEY, now()->addMonth(), function () {
            cache()->forget('country_names_ar');

            $path = base_path('vendor/umpirsky/country-list/data/ar/country.php');

            if (! is_file($path)) {
                return [];
            }

            $list = require $path;

            return is_array($list) ? $list : [];
        });
    }

    public static function forCode(?string $countryCode): ?string
    {
        if (! $countryCode) {
            return null;
        }

        $code = strtoupper(trim($countryCode));
        $name = self::arabicNames()[$code] ?? $code;

        return self::ensureUtf8($name);
    }

    public static function formatWorkStart(?string $raw, string $default = '09:00'): string
    {
        if ($raw === null || trim((string) $raw) === '') {
            return $default;
        }

        $raw = self::ensureUtf8(trim((string) $raw));

        try {
            return Carbon::parse($raw)->format('H:i');
        } catch (\Throwable) {
            if (preg_match('/^\d{1,2}:\d{2}/', $raw, $matches)) {
                return substr($matches[0], 0, 5);
            }

            return $default;
        }
    }

    public static function ensureUtf8(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        return mb_convert_encoding($value, 'UTF-8', 'UTF-8');
    }
}
