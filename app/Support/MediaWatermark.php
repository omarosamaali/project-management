<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Throwable;

class MediaWatermark
{
    public const BRAND_ACADEMY = 'academy';

    public const BRAND_APP = 'app';

    public static function brandForPath(string $relativePath): ?string
    {
        $path = str_replace('\\', '/', ltrim($relativePath, '/'));

        foreach (config('watermark.skip_prefixes', []) as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return null;
            }
        }

        $basename = strtolower(basename($path));
        foreach (config('watermark.skip_filename_contains', []) as $needle) {
            if ($needle !== '' && str_contains($basename, strtolower($needle))) {
                return null;
            }
        }

        // Never watermark the uploaded academy logo asset itself.
        try {
            $academyLogo = \App\Models\Setting::academyLogoPath();
            if ($academyLogo && str_replace('\\', '/', $academyLogo) === $path) {
                return null;
            }
        } catch (Throwable) {
            // settings table may be missing during early bootstrap
        }

        foreach (config('watermark.academy_prefixes', []) as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return self::BRAND_ACADEMY;
            }
        }

        return self::BRAND_APP;
    }

    public static function shouldWatermark(string $relativePath): bool
    {
        return self::brandForPath($relativePath) !== null
            && self::isSupportedMedia($relativePath);
    }

    public static function isSupportedMedia(string $path): bool
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($ext, self::imageExtensions(), true)
            || in_array($ext, self::videoExtensions(), true);
    }

    public static function imageExtensions(): array
    {
        return ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    }

    public static function videoExtensions(): array
    {
        return ['mp4', 'webm', 'mov', 'm4v', 'avi', 'mkv'];
    }

    public static function markerPath(string $absolutePath): string
    {
        return $absolutePath . '.wm';
    }

    public static function isMarked(string $absolutePath): bool
    {
        return is_file(self::markerPath($absolutePath));
    }

    public static function mark(string $absolutePath, string $brand): void
    {
        File::put(self::markerPath($absolutePath), json_encode([
            'brand' => $brand,
            'at' => now()->toIso8601String(),
        ]));
    }

    /**
     * @return array{ok:bool,skipped?:bool,reason?:string,brand?:string}
     */
    public static function applyAbsolute(string $absolutePath, ?string $brand = null, bool $force = false): array
    {
        if (! is_file($absolutePath)) {
            return ['ok' => false, 'reason' => 'missing'];
        }

        $brand ??= self::BRAND_APP;

        if (! config('watermark.bake', false)) {
            return ['ok' => true, 'skipped' => true, 'reason' => 'bake_disabled', 'brand' => $brand];
        }

        if (! $force && self::isMarked($absolutePath)) {
            return ['ok' => true, 'skipped' => true, 'reason' => 'already', 'brand' => $brand];
        }

        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        try {
            if (in_array($ext, self::imageExtensions(), true)) {
                self::applyToImage($absolutePath, $brand);
            } elseif (in_array($ext, self::videoExtensions(), true)) {
                self::applyToVideo($absolutePath, $brand);
            } else {
                return ['ok' => false, 'reason' => 'unsupported'];
            }

            self::mark($absolutePath, $brand);

            return ['ok' => true, 'brand' => $brand];
        } catch (Throwable $e) {
            Log::warning('Watermark failed: '.$e->getMessage(), ['path' => $absolutePath]);

            return ['ok' => false, 'reason' => $e->getMessage()];
        }
    }

    /**
     * Apply watermark to a file on the public disk (relative path).
     *
     * @return array{ok:bool,skipped?:bool,reason?:string,brand?:string}
     */
    public static function applyDiskPath(string $relativePath, string $disk = 'public', bool $force = false): array
    {
        $relativePath = str_replace('\\', '/', ltrim($relativePath, '/'));
        $brand = self::brandForPath($relativePath);

        if ($brand === null) {
            return ['ok' => true, 'skipped' => true, 'reason' => 'skip'];
        }

        if (! self::isSupportedMedia($relativePath)) {
            return ['ok' => true, 'skipped' => true, 'reason' => 'unsupported'];
        }

        $absolute = storage_path('app/'.$disk.'/'.$relativePath);

        return self::applyAbsolute($absolute, $brand, $force);
    }

    public static function applyPublicRelative(string $relativeUnderPublic, bool $force = false): array
    {
        $relativeUnderPublic = str_replace('\\', '/', ltrim($relativeUnderPublic, '/'));
        $brand = self::brandForPath($relativeUnderPublic) ?? self::BRAND_APP;
        $absolute = public_path($relativeUnderPublic);

        if (! self::isSupportedMedia($absolute)) {
            return ['ok' => true, 'skipped' => true, 'reason' => 'unsupported'];
        }

        return self::applyAbsolute($absolute, $brand, $force);
    }

    protected static function logoPath(string $brand): string
    {
        $path = (string) config('watermark.logos.'.$brand, '');

        if (! is_file($path)) {
            throw new \RuntimeException("Watermark logo missing: {$path}");
        }

        return $path;
    }

    protected static function applyToImage(string $absolutePath, string $brand): void
    {
        $logoFile = self::logoPath($brand);
        $src = self::loadImage($absolutePath);
        $logo = self::loadImage($logoFile);

        $sw = imagesx($src);
        $sh = imagesy($src);
        $lw = imagesx($logo);
        $lh = imagesy($logo);

        $targetW = (int) max(
            (int) config('watermark.min_logo_width', 48),
            min(
                (int) config('watermark.max_logo_width', 220),
                (int) round($sw * (float) config('watermark.width_ratio', 0.18))
            )
        );
        $targetH = (int) max(1, (int) round($lh * ($targetW / max(1, $lw))));

        $scaled = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($scaled, false);
        imagesavealpha($scaled, true);
        $transparent = imagecolorallocatealpha($scaled, 0, 0, 0, 127);
        imagefilledrectangle($scaled, 0, 0, $targetW, $targetH, $transparent);
        imagealphablending($scaled, true);
        imagecopyresampled($scaled, $logo, 0, 0, 0, 0, $targetW, $targetH, $lw, $lh);

        $opacity = max(0.05, min(1.0, (float) config('watermark.opacity', 0.38)));
        self::multiplyAlpha($scaled, $opacity);

        $margin = (int) config('watermark.margin', 16);
        $x = max(0, $sw - $targetW - $margin);
        $y = $margin;

        imagealphablending($src, true);
        imagesavealpha($src, true);
        imagecopy($src, $scaled, $x, $y, 0, 0, $targetW, $targetH);

        self::saveImage($src, $absolutePath);

        imagedestroy($scaled);
        imagedestroy($logo);
        imagedestroy($src);
    }

    protected static function applyToVideo(string $absolutePath, string $brand): void
    {
        $ffmpeg = self::resolveFfmpeg();
        if (! $ffmpeg) {
            throw new \RuntimeException('ffmpeg not available; install ffmpeg or set FFMPEG_PATH');
        }

        $logoFile = self::logoPath($brand);
        $opacity = max(0.05, min(1.0, (float) config('watermark.opacity', 0.38)));
        $margin = (int) config('watermark.margin', 16);
        $logoW = (int) config('watermark.max_logo_width', 220);

        $dir = dirname($absolutePath);
        $tmp = $dir.DIRECTORY_SEPARATOR.uniqid('wm_', true).'.'.pathinfo($absolutePath, PATHINFO_EXTENSION);
        $scaledLogo = $dir.DIRECTORY_SEPARATOR.uniqid('wmlogo_', true).'.png';

        // Pre-scale the logo with GD — huge brand PNGs crash ffmpeg overlays on Windows.
        self::writeScaledLogoPng($logoFile, $scaledLogo, $logoW);

        $filter = sprintf(
            '[1:v]format=rgba,colorchannelmixer=aa=%.3f[wm];[0:v][wm]overlay=W-w-%d:%d:format=auto',
            $opacity,
            $margin,
            $margin
        );

        $cmd = [
            $ffmpeg,
            '-y',
            '-hide_banner',
            '-loglevel', 'error',
            '-i', $absolutePath,
            '-i', $scaledLogo,
            '-filter_complex', $filter,
            '-c:v', 'libx264',
            '-preset', 'ultrafast',
            '-crf', '28',
            '-c:a', 'copy',
            '-movflags', '+faststart',
            $tmp,
        ];

        $descriptor = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = proc_open($cmd, $descriptor, $pipes, null, null, ['bypass_shell' => true]);
        if (! is_resource($process)) {
            @unlink($scaledLogo);
            throw new \RuntimeException('Failed to start ffmpeg');
        }

        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $code = proc_close($process);
        @unlink($scaledLogo);

        if ($code !== 0 || ! is_file($tmp) || filesize($tmp) < 1) {
            @unlink($tmp);
            $hint = trim((string) $stderr);
            throw new \RuntimeException(
                'ffmpeg watermark failed (exit '.$code.')'.($hint !== '' ? ': '.$hint : '')
            );
        }

        // Replace original (Windows often locks rename-over-existing).
        if (is_file($absolutePath)) {
            @chmod($absolutePath, 0666);
            if (! @unlink($absolutePath)) {
                // Fall back to overwrite-via-copy into place.
                if (! @copy($tmp, $absolutePath)) {
                    @unlink($tmp);
                    throw new \RuntimeException('Unable to replace original video (file locked)');
                }
                @unlink($tmp);

                return;
            }
        }

        if (! @rename($tmp, $absolutePath)) {
            if (! @copy($tmp, $absolutePath)) {
                @unlink($tmp);
                throw new \RuntimeException('Unable to move watermarked video into place');
            }
            @unlink($tmp);
        }
    }

    protected static function writeScaledLogoPng(string $logoFile, string $destPng, int $targetW): void
    {
        $logo = self::loadImage($logoFile);
        $lw = imagesx($logo);
        $lh = imagesy($logo);
        $targetW = max(24, $targetW);
        $targetH = (int) max(1, (int) round($lh * ($targetW / max(1, $lw))));

        $scaled = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($scaled, false);
        imagesavealpha($scaled, true);
        $transparent = imagecolorallocatealpha($scaled, 0, 0, 0, 127);
        imagefilledrectangle($scaled, 0, 0, $targetW, $targetH, $transparent);
        imagealphablending($scaled, true);
        imagecopyresampled($scaled, $logo, 0, 0, 0, 0, $targetW, $targetH, $lw, $lh);

        if (! imagepng($scaled, $destPng, 6)) {
            imagedestroy($scaled);
            imagedestroy($logo);
            throw new \RuntimeException('Unable to write scaled watermark logo');
        }

        imagedestroy($scaled);
        imagedestroy($logo);
    }

    protected static function resolveFfmpeg(): ?string
    {
        $configured = (string) config('watermark.ffmpeg_path', 'ffmpeg');
        if ($configured !== '' && self::ffmpegWorks($configured)) {
            return $configured;
        }

        foreach ([
            'ffmpeg',
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\Program Files\\ffmpeg\\bin\\ffmpeg.exe',
        ] as $candidate) {
            if (self::ffmpegWorks($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    protected static function ffmpegWorks(string $bin): bool
    {
        $descriptor = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = @proc_open([$bin, '-version'], $descriptor, $pipes, null, null, ['bypass_shell' => true]);
        if (! is_resource($process)) {
            return false;
        }
        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        return proc_close($process) === 0;
    }

    protected static function loadImage(string $path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $img = match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => @imagecreatefromwebp($path),
            'gif' => @imagecreatefromgif($path),
            default => false,
        };

        if (! $img) {
            throw new \RuntimeException("Cannot read image: {$path}");
        }

        imagealphablending($img, true);
        imagesavealpha($img, true);

        return $img;
    }

    protected static function saveImage($img, string $path): void
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $ok = match ($ext) {
            'jpg', 'jpeg' => imagejpeg($img, $path, 90),
            'png' => imagepng($img, $path, 6),
            'webp' => imagewebp($img, $path, 90),
            'gif' => imagegif($img, $path),
            default => false,
        };

        if (! $ok) {
            throw new \RuntimeException("Cannot write image: {$path}");
        }
    }

    protected static function multiplyAlpha($img, float $opacity): void
    {
        $w = imagesx($img);
        $h = imagesy($img);
        imagealphablending($img, false);
        imagesavealpha($img, true);

        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $rgba = imagecolorat($img, $x, $y);
                $a = ($rgba & 0x7F000000) >> 24;
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;
                // GD alpha: 0 opaque … 127 transparent
                $newA = (int) min(127, round(127 - ((127 - $a) * $opacity)));
                $color = imagecolorallocatealpha($img, $r, $g, $b, $newA);
                imagesetpixel($img, $x, $y, $color);
            }
        }
    }
}
