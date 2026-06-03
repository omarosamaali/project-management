<?php

namespace App\Support;

class CountryNames
{
    private const CACHE_KEY = 'country_names_ar_v2';

    /**
     * @return array<string, string>
     */
    public static function arabicNames(): array
    {
        return cache()->remember(self::CACHE_KEY, now()->addMonth(), function () {
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
