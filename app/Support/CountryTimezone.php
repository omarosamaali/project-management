<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CountryTimezone
{
    /** @var array<string, string> رمز الدولة => المنطقة الزمنية الأساسية */
    private const MAP = [
        'EG' => 'Africa/Cairo',
        'SA' => 'Asia/Riyadh',
        'AE' => 'Asia/Dubai',
        'KW' => 'Asia/Kuwait',
        'QA' => 'Asia/Qatar',
        'BH' => 'Asia/Bahrain',
        'OM' => 'Asia/Muscat',
        'JO' => 'Asia/Amman',
        'LB' => 'Asia/Beirut',
        'SY' => 'Asia/Damascus',
        'IQ' => 'Asia/Baghdad',
        'YE' => 'Asia/Aden',
        'PS' => 'Asia/Gaza',
        'LY' => 'Africa/Tripoli',
        'TN' => 'Africa/Tunis',
        'DZ' => 'Africa/Algiers',
        'MA' => 'Africa/Casablanca',
        'SD' => 'Africa/Khartoum',
        'TR' => 'Europe/Istanbul',
        'US' => 'America/New_York',
        'GB' => 'Europe/London',
        'FR' => 'Europe/Paris',
        'DE' => 'Europe/Berlin',
        'IN' => 'Asia/Kolkata',
        'PK' => 'Asia/Karachi',
        'AO' => 'Africa/Luanda',
    ];

    public static function timezoneForCountry(?string $countryCode): string
    {
        $code = strtoupper(trim((string) $countryCode));

        if ($code !== '' && isset(self::MAP[$code])) {
            return self::MAP[$code];
        }

        return config('app.timezone', 'UTC');
    }

    public static function localNow(?string $countryCode, ?User $user = null): array
    {
        $code = strtoupper(trim((string) $countryCode));
        $timezone = self::timezoneForCountry($code);
        $now = Carbon::now($timezone);

        $workStart = '09:00:00';
        if ($user) {
            $raw = $user->work_start_time ?? null;
            if ($raw && trim((string) $raw) !== '') {
                $workStart = Carbon::parse($raw)->format('H:i:s');
            }
        }

        return [
            'country_code' => $code,
            'timezone' => $timezone,
            'date' => $now->format('Y-m-d'),
            'time' => $now->format('H:i'),
            'time_full' => $now->format('H:i:s'),
            'datetime_label' => $now->locale('ar')->isoFormat('dddd D MMMM YYYY — h:mm:ss a'),
            'work_start' => substr($workStart, 0, 5),
            'work_start_full' => $workStart,
        ];
    }

    /**
     * محاولة تحديد الدولة والتوقيت من IP الزائر (للعرض الافتراضي عند فتح الشاشة).
     */
    public static function detectFromIp(?string $ip): ?array
    {
        if (!$ip || in_array($ip, ['127.0.0.1', '::1'], true)) {
            return null;
        }

        try {
            $response = Http::timeout(4)->get("https://ipapi.co/{$ip}/json/");

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();
            $code = strtoupper((string) ($data['country_code'] ?? ''));
            $timezone = (string) ($data['timezone'] ?? self::timezoneForCountry($code));

            if ($code === '') {
                return null;
            }

            $now = Carbon::now($timezone);

            return [
                'country_code' => $code,
                'country_name' => $data['country_name'] ?? $code,
                'timezone' => $timezone,
                'date' => $now->format('Y-m-d'),
                'time' => $now->format('H:i'),
                'time_full' => $now->format('H:i:s'),
                'datetime_label' => $now->locale('ar')->isoFormat('dddd D MMMM YYYY — h:mm:ss a'),
                'source' => 'ip',
            ];
        } catch (\Throwable $e) {
            Log::info('[CountryTimezone] IP lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);

            return null;
        }
    }
}
