<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

/**
 * Normalize lesson video sources (uploaded file URL, direct media URL, YouTube, Vimeo)
 * so the trainee player can use one Plyr instance for all of them.
 */
class LessonVideoSource
{
    public const PROVIDER_HTML5 = 'html5';
    public const PROVIDER_YOUTUBE = 'youtube';
    public const PROVIDER_VIMEO = 'vimeo';

    /**
     * @return array{provider: string, src: string, embed_id?: string}|null
     */
    public static function fromUrl(?string $url): ?array
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if ($id = self::youtubeId($url)) {
            return [
                'provider' => self::PROVIDER_YOUTUBE,
                'src' => $url,
                'embed_id' => $id,
            ];
        }

        if ($id = self::vimeoId($url)) {
            return [
                'provider' => self::PROVIDER_VIMEO,
                'src' => $url,
                'embed_id' => $id,
            ];
        }

        // Direct / streamable media URL — same HTML5 <video> as uploads
        return [
            'provider' => self::PROVIDER_HTML5,
            'src' => $url,
        ];
    }

    /**
     * Resolve duration in seconds for an embed/external URL (YouTube, Vimeo, or direct media).
     */
    public static function resolveDurationSeconds(?string $url): ?int
    {
        $parsed = self::fromUrl($url);
        if (!$parsed) {
            return null;
        }

        try {
            if ($parsed['provider'] === self::PROVIDER_VIMEO) {
                $response = Http::timeout(8)->get('https://vimeo.com/api/oembed.json', [
                    'url' => $parsed['src'],
                ]);
                if ($response->successful() && isset($response['duration'])) {
                    return max(0, (int) $response['duration']);
                }

                return null;
            }

            if ($parsed['provider'] === self::PROVIDER_YOUTUBE) {
                $id = $parsed['embed_id'] ?? null;
                if (!$id) {
                    return null;
                }

                $response = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (compatible; BladeCourseBot/1.0)',
                        'Accept-Language' => 'en-US,en;q=0.9',
                    ])
                    ->get('https://www.youtube.com/watch', ['v' => $id]);

                if (!$response->successful()) {
                    return null;
                }

                $html = $response->body();
                if (preg_match('/"lengthSeconds":\s*"?(\d+)"?/', $html, $m)) {
                    return max(0, (int) $m[1]);
                }

                return null;
            }

            // Direct media: HEAD/range is unreliable; browser metadata is preferred.
            return null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function youtubeId(string $url): ?string
    {
        $patterns = [
            '/youtu\.be\/([A-Za-z0-9_-]{6,})/i',
            '/youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|live\/)([A-Za-z0-9_-]{6,})/i',
            '/youtube-nocookie\.com\/embed\/([A-Za-z0-9_-]{6,})/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    public static function vimeoId(string $url): ?string
    {
        if (preg_match('/vimeo\.com\/(?:video\/)?(\d+)/i', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Platform poster image for embed URLs (YouTube / Vimeo) — highest available quality.
     */
    public static function thumbnailUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if ($id = self::youtubeId($url)) {
            return self::youtubeThumbnailUrl($id);
        }

        if ($id = self::vimeoId($url)) {
            try {
                $response = Http::timeout(8)->get('https://vimeo.com/api/oembed.json', [
                    'url' => 'https://vimeo.com/' . $id,
                    'width' => 1920,
                ]);
                if ($response->successful() && !empty($response['thumbnail_url'])) {
                    $thumb = (string) $response['thumbnail_url'];
                    // Prefer largest Vimeo thumbnail variant when URL contains size tokens
                    $thumb = preg_replace('/_\d+x\d+/', '_1280x720', $thumb) ?: $thumb;

                    return $thumb;
                }
            } catch (\Throwable) {
                // fall through
            }

            return "https://vumbnail.com/{$id}.jpg";
        }

        return null;
    }

    /**
     * Pick the best available YouTube still (maxres → sd → hq).
     */
    public static function youtubeThumbnailUrl(string $id): string
    {
        $candidates = [
            "https://i.ytimg.com/vi/{$id}/maxresdefault.jpg",
            "https://i.ytimg.com/vi/{$id}/sddefault.jpg",
            "https://i.ytimg.com/vi/{$id}/hqdefault.jpg",
        ];

        foreach ($candidates as $candidate) {
            try {
                $response = Http::timeout(4)->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; BladeCourseBot/1.0)',
                ])->head($candidate);

                if (!$response->successful()) {
                    continue;
                }

                // Missing maxres often returns a tiny grey placeholder with HTTP 200
                $length = (int) ($response->header('Content-Length') ?: 0);
                if ($length === 0 || $length > 8000) {
                    return $candidate;
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return $candidates[2];
    }
}
