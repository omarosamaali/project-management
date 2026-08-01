<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Hardens HTML5 lesson/promo video delivery against casual download tools (IDM, FDM, wget, …).
 *
 * Note: anything a browser can play can still be captured (screen record / DevTools).
 * This stack removes direct public file URLs, rejects known downloaders, and keeps playback session-bound.
 */
class VideoDownloadGuard
{
    /** Relative dirs that must never be served as static public files. */
    public const PROTECTED_DIRS = [
        'courses/path-videos',
        'courses/videos',
    ];

    /**
     * User-Agent fragments used by download managers / CLI fetchers.
     *
     * @var list<string>
     */
    protected static array $blockedAgents = [
        'idm',
        'idman',
        'internet download manager',
        'download manager',
        'fdm',
        'free download manager',
        'flashget',
        'getright',
        'eagleget',
        'orbit downloader',
        'jdownloader',
        'dashdownloader',
        'miot',
        'massive downloader',
        'wget',
        'curl/',
        'libwww-perl',
        'python-urllib',
        'python-requests',
        'go-http-client',
        'aria2',
        'axel',
        'puf/',
        'fetch libfetch',
        'httperf',
        'scrapy',
        'httpclient',
        'okhttp',
        'java/',
        'apache-httpclient',
        'powershell',
        'postmanruntime',
        'insomnia',
        'paw/',
        'thunder',
        'nqdownload',
        'nsplayer',
        'lavf',
        'ffmpeg',
        'vlc/',
        'mpv',
        'quicktime',
    ];

    public static function assertPlaybackRequest(?Request $request = null): void
    {
        $request ??= request();

        $ua = strtolower((string) $request->userAgent());
        if ($ua === '' || self::isBlockedAgent($ua)) {
            throw new HttpException(403, 'Video download tools are not allowed.');
        }

        // Real browsers send Accept that includes video, html, or */* for media elements.
        $accept = strtolower((string) $request->header('Accept', ''));
        if ($accept !== '' && ! self::acceptLooksLikeBrowser($accept)) {
            throw new HttpException(403, 'Invalid playback request.');
        }

        // When a Referer is present, require same host (blocks many hotlink/downloaders).
        $referer = (string) $request->headers->get('Referer', '');
        if ($referer !== '' && ! self::refererIsSameSite($referer, $request)) {
            throw new HttpException(403, 'Invalid playback origin.');
        }
    }

    public static function isBlockedAgent(string $ua): bool
    {
        foreach (self::$blockedAgents as $needle) {
            if (str_contains($ua, $needle)) {
                return true;
            }
        }

        return false;
    }

    protected static function acceptLooksLikeBrowser(string $accept): bool
    {
        return str_contains($accept, 'video/')
            || str_contains($accept, 'application/octet-stream')
            || str_contains($accept, 'text/html')
            || str_contains($accept, '*/*')
            || str_contains($accept, 'application/xhtml');
    }

    protected static function refererIsSameSite(string $referer, Request $request): bool
    {
        $refHost = parse_url($referer, PHP_URL_HOST);
        if (! is_string($refHost) || $refHost === '') {
            return false;
        }

        $appHost = $request->getHost();

        return strcasecmp($refHost, $appHost) === 0;
    }

    /**
     * Resolve a lesson/promo video path from private disk first, then legacy public disk.
     */
    public static function absolutePath(string $relative): ?string
    {
        $relative = ltrim(str_replace('\\', '/', $relative), '/');
        if ($relative === '') {
            return null;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($relative)) {
                $full = Storage::disk($disk)->path($relative);

                return is_file($full) ? $full : null;
            }
        }

        return null;
    }

    /** Delete a protected video from private and legacy public disks. */
    public static function deleteStored(string $relative): void
    {
        $relative = ltrim(str_replace('\\', '/', $relative), '/');
        if ($relative === '') {
            return;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($relative)) {
                Storage::disk($disk)->delete($relative);
            }
        }
    }

    /**
     * Preferred disk for newly uploaded protected videos.
     */
    public static function storageDisk(): string
    {
        return 'local';
    }

    /**
     * Stream a local video file with anti-download response headers (Range/seeking kept).
     */
    public static function fileResponse(string $absolutePath): Response
    {
        self::assertPlaybackRequest();

        abort_unless(is_file($absolutePath), 404);

        $mime = mime_content_type($absolutePath) ?: 'video/mp4';

        return response()->file($absolutePath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="lesson"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
            // Discourage plugins / managers that honor these hints
            'X-Download-Options' => 'noopen',
        ]);
    }
}
