<?php

namespace App\Support;

class YouTubeLive
{
    public static function openBeforeMinutes(): int
    {
        return max(0, (int) config('courses.meeting.open_before_minutes', 30));
    }

    public static function videoId(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        return LessonVideoSource::youtubeId($url);
    }

    public static function isYouTubeUrl(?string $url): bool
    {
        return self::videoId($url) !== null;
    }

    /**
     * Embeddable YouTube player URL suitable for live streams and VODs.
     */
    public static function embedUrl(?string $url): ?string
    {
        $id = self::videoId($url);
        if (!$id) {
            return null;
        }

        return 'https://www.youtube.com/embed/' . rawurlencode($id)
            . '?autoplay=1&rel=0&modestbranding=1&playsinline=1';
    }

    public static function watchUrl(?string $url): ?string
    {
        $id = self::videoId($url);

        return $id ? 'https://www.youtube.com/watch?v=' . $id : null;
    }
}
