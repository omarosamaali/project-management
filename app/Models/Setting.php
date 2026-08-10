<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function hasStorage(): bool
    {
        try {
            return Schema::hasTable((new static())->getTable());
        } catch (Throwable) {
            return false;
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        if (!static::hasStorage()) {
            return $default;
        }

        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        if (!static::hasStorage()) {
            return;
        }

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::set((string) $key, $value);
        }
    }

    public static function academyLogoPath(): ?string
    {
        return static::get('academy_logo_path');
    }

    public static function academyHeroImagePath(): ?string
    {
        return static::get('academy_hero_image_path');
    }

    public static function academyLogoUrl(?string $fallbackAsset = null): string
    {
        $path = static::academyLogoPath();

        if ($path) {
            return Storage::url($path);
        }

        return asset($fallbackAsset ?: 'assets/images/academy_logo.png');
    }

    /**
     * Logo for dark chrome (academy public navbar / dashboard sidebar).
     * Falls back to the white academy mark when none is uploaded.
     */
    public static function academyChromeLogoUrl(): string
    {
        return static::academyLogoUrl('assets/images/evorq_academy_logo_white.png');
    }

    public static function academyHeroImageUrl(): string
    {
        $path = static::academyHeroImagePath();

        if ($path) {
            return Storage::url($path);
        }

        return 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80';
    }

    /**
     * Absolute HTTPS hero URL for WhatsApp / email (Meta cannot fetch localhost paths).
     */
    public static function academyHeroImagePublicUrl(): string
    {
        $path = static::academyHeroImagePath();
        $fallback = (string) config(
            'services.whatsapp_academy.default_image',
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80'
        );

        if (! $path) {
            return $fallback;
        }

        $relative = Storage::url($path);
        if (str_starts_with($relative, 'http://') || str_starts_with($relative, 'https://')) {
            $absolute = $relative;
        } else {
            $absolute = rtrim(\App\Support\AppDomains::liveAcademyBase(), '/').'/'.ltrim($relative, '/');
        }

        // WhatsApp media fetch requires a public HTTPS URL.
        if (str_starts_with($absolute, 'http://')) {
            $absolute = 'https://'.substr($absolute, strlen('http://'));
        }

        $host = parse_url($absolute, PHP_URL_HOST);
        if (is_string($host) && in_array(strtolower($host), ['localhost', '127.0.0.1'], true)) {
            return $fallback;
        }

        return $absolute;
    }

    public static function academyTrainerProfitPercentage(): float
    {
        return max(0, min(100, (float) static::get('academy_trainer_profit_percentage', 50)));
    }

    /**
     * Trainer profit % for a course location_type.
     * Onsite returns 0 until admin sets a value (empty setting).
     *
     * @param  string  $locationType  online|recorded|on_site|private
     */
    public static function academyTrainerProfitPercentageFor(string $locationType): float
    {
        $map = [
            'online' => 'academy_trainer_profit_online',
            'recorded' => 'academy_trainer_profit_recorded',
            'private' => 'academy_trainer_profit_private',
            'on_site' => 'academy_trainer_profit_onsite',
        ];

        $key = $map[$locationType] ?? null;
        if (! $key) {
            return static::academyTrainerProfitPercentage();
        }

        $defaults = [
            'academy_trainer_profit_online' => 60,
            'academy_trainer_profit_recorded' => 50,
            'academy_trainer_profit_private' => 70,
            'academy_trainer_profit_onsite' => null,
        ];

        $raw = static::get($key, null);

        // Migrate once from legacy global for online/recorded/private if never set.
        if ($raw === null && $key !== 'academy_trainer_profit_onsite' && static::get('academy_trainer_profit_percentage') !== null) {
            $legacy = static::academyTrainerProfitPercentage();
            // Only use legacy as soft default when type-specific never stored; do not write here.
            if ($defaults[$key] === null) {
                return 0.0;
            }
            // Prefer the new type defaults over legacy when key missing.
            $raw = $defaults[$key];
        }

        if ($key === 'academy_trainer_profit_onsite') {
            if ($raw === null || $raw === '') {
                return 0.0;
            }

            return max(0, min(100, (float) $raw));
        }

        if ($raw === null || $raw === '') {
            return (float) ($defaults[$key] ?? 50);
        }

        return max(0, min(100, (float) $raw));
    }

    /**
     * @return array{online: float, recorded: float, private: float, onsite: float|null}
     */
    public static function academyTrainerProfitPercentages(): array
    {
        $onsiteRaw = static::get('academy_trainer_profit_onsite', null);

        return [
            'online' => static::academyTrainerProfitPercentageFor('online'),
            'recorded' => static::academyTrainerProfitPercentageFor('recorded'),
            'private' => static::academyTrainerProfitPercentageFor('private'),
            'onsite' => ($onsiteRaw === null || $onsiteRaw === '') ? null : static::academyTrainerProfitPercentageFor('on_site'),
        ];
    }

    public static function academyTrainerCashoutMinimum(): float
    {
        $raw = static::get('academy_trainer_cashout_minimum', null);

        return ($raw === null || $raw === '') ? 100.0 : max(0, (float) $raw);
    }

    public static function academyTrainerCashoutMaximum(): float
    {
        $raw = static::get('academy_trainer_cashout_maximum', null);

        return ($raw === null || $raw === '') ? 10000.0 : max(0, (float) $raw);
    }

    /**
     * Admin toggle: use embedded meeting app for private courses (testing feature).
     */
    public static function academyEmbeddedMeetingsEnabled(): bool
    {
        return (string) static::get('academy_embedded_meetings_enabled', '0') === '1';
    }

    /**
     * Toggle is on and meeting API credentials are configured.
     */
    public static function academyEmbeddedMeetingsActive(): bool
    {
        if (! static::academyEmbeddedMeetingsEnabled()) {
            return false;
        }

        $base = (string) config('services.meeting.base_url', '');
        $key = (string) config('services.meeting.api_key', '');
        $secret = (string) config('services.meeting.api_secret', '');

        return $base !== '' && $key !== '' && $secret !== '';
    }
}
