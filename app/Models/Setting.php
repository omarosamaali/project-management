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

    public static function academyTrainerProfitPercentage(): float
    {
        return max(0, min(100, (float) static::get('academy_trainer_profit_percentage', 50)));
    }
}
