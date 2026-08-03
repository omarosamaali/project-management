<?php

namespace App\Support;

use Illuminate\Http\Request;

class AppDomains
{
    public static function mainUrl(?string $path = null): string
    {
        return self::join(self::mainBase(), $path);
    }

    public static function academyUrl(?string $path = null): string
    {
        return self::join(self::academyBase(), $path);
    }

    public static function mainBase(): string
    {
        $base = rtrim((string) config('app.url'), '/');

        return $base !== '' ? $base : 'https://evorq.online';
    }

    public static function academyBase(): string
    {
        $base = rtrim((string) config('app.academy_url'), '/');

        return $base !== '' ? $base : self::mainBase();
    }

    /**
     * Prefer live domains for emails/WhatsApp when the configured URL is localhost.
     */
    public static function liveMainBase(): string
    {
        $base = self::mainBase();

        return self::isLocalHost($base) ? 'https://evorq.online' : $base;
    }

    public static function liveAcademyBase(): string
    {
        $base = self::academyBase();
        if (self::isLocalHost($base)) {
            return 'https://evorqacademy.com';
        }

        return $base;
    }

    public static function mainHost(): string
    {
        return self::hostFromBase(self::mainBase());
    }

    public static function academyHost(): string
    {
        return self::hostFromBase(self::academyBase());
    }

    /**
     * True when main and academy are different hosts (production split enabled).
     */
    public static function enabled(): bool
    {
        $main = self::mainHost();
        $academy = self::academyHost();

        return $main !== '' && $academy !== '' && strcasecmp($main, $academy) !== 0;
    }

    public static function isAcademyRequest(?Request $request = null): bool
    {
        $request ??= request();
        if (! $request) {
            return false;
        }

        return self::enabled() && self::isAcademyHost($request->getHost());
    }

    public static function isMainRequest(?Request $request = null): bool
    {
        $request ??= request();
        if (! $request) {
            return true;
        }

        if (! self::enabled()) {
            return true;
        }

        return self::isMainHost($request->getHost());
    }

    public static function isAcademyHost(?string $host): bool
    {
        $host = self::normalizeHost($host);

        return $host !== '' && strcasecmp($host, self::academyHost()) === 0;
    }

    public static function isMainHost(?string $host): bool
    {
        $host = self::normalizeHost($host);

        return $host !== '' && strcasecmp($host, self::mainHost()) === 0;
    }

    /**
     * Paths that must live on the academy domain when split is enabled.
     */
    public static function isAcademyOnlyPath(string $path): bool
    {
        $path = '/'.ltrim(strtok($path, '?') ?: '', '/');
        if ($path === '/') {
            return false;
        }

        $prefixes = [
            '/academy',
            '/private-requests',
        ];

        foreach ($prefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        if (preg_match('#^/courses/[^/]+$#', $path)) {
            return true;
        }
        if (preg_match('#^/courses/[^/]+/private-request#', $path)) {
            return true;
        }
        if (preg_match('#^/courses/[^/]+/video/stream#', $path)) {
            return true;
        }

        return false;
    }

    /**
     * Paths that must live on the main domain when split is enabled.
     */
    public static function isMainOnlyPath(string $path): bool
    {
        $path = '/'.ltrim(strtok($path, '?') ?: '', '/');
        if ($path === '/') {
            return false;
        }

        $prefixes = [
            '/system',
            '/special-request',
            '/special-requests',
            '/stores',
            '/send-whatsapp-otp',
            '/send-otp',
            '/partner',
            '/project-meetings',
            '/proposals',
            '/issues',
            '/meetings',
        ];

        foreach ($prefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }

    protected static function join(string $base, ?string $path): string
    {
        if ($path === null || $path === '' || $path === '/') {
            return $base;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return $base.'/'.ltrim($path, '/');
    }

    protected static function hostFromBase(string $base): string
    {
        $host = parse_url($base, PHP_URL_HOST);

        return self::normalizeHost(is_string($host) ? $host : '');
    }

    protected static function normalizeHost(?string $host): string
    {
        $host = strtolower(trim((string) $host));
        if (str_starts_with($host, '[')) {
            return $host;
        }

        return explode(':', $host)[0] ?? '';
    }

    protected static function isLocalHost(string $baseOrHost): bool
    {
        $host = str_contains($baseOrHost, '://')
            ? (string) parse_url($baseOrHost, PHP_URL_HOST)
            : $baseOrHost;

        $host = self::normalizeHost($host);

        return $host === 'localhost'
            || $host === '127.0.0.1'
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.local');
    }
}
