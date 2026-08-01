<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CurrencyRate
{
    /**
     * How many EGP equal 1 AED.
     */
    public static function aedToEgp(): ?float
    {
        return Cache::remember('fx:aed_egp_rate', now()->addHours(6), function () {
            return self::fetchAedToEgp();
        });
    }

    public static function aedToEgpMeta(): array
    {
        $rate = self::aedToEgp();

        return [
            'base' => 'AED',
            'quote' => 'EGP',
            'rate' => $rate,
            'ok' => $rate !== null && $rate > 0,
        ];
    }

    protected static function fetchAedToEgp(): ?float
    {
        $sources = [
            function () {
                $res = Http::timeout(8)->acceptJson()->get('https://open.er-api.com/v6/latest/AED');
                if (! $res->ok()) {
                    return null;
                }
                $rate = data_get($res->json(), 'rates.EGP');

                return is_numeric($rate) ? (float) $rate : null;
            },
            function () {
                $res = Http::timeout(8)->acceptJson()->get(
                    'https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/aed.min.json'
                );
                if (! $res->ok()) {
                    return null;
                }
                $rate = data_get($res->json(), 'aed.egp');

                return is_numeric($rate) ? (float) $rate : null;
            },
            function () {
                $res = Http::timeout(8)->acceptJson()->get('https://api.exchangerate-api.com/v4/latest/AED');
                if (! $res->ok()) {
                    return null;
                }
                $rate = data_get($res->json(), 'rates.EGP');

                return is_numeric($rate) ? (float) $rate : null;
            },
        ];

        foreach ($sources as $source) {
            try {
                $rate = $source();
                if ($rate !== null && $rate > 0) {
                    return round($rate, 6);
                }
            } catch (Throwable $e) {
                Log::warning('AED→EGP rate fetch failed: '.$e->getMessage());
            }
        }

        return null;
    }
}
