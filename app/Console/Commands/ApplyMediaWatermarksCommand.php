<?php

namespace App\Console\Commands;

use App\Support\MediaWatermark;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ApplyMediaWatermarksCommand extends Command
{
    protected $signature = 'media:apply-watermarks
        {--force : Re-apply even if already marked}
        {--dry-run : List files without writing}';

    protected $description = 'Bake brand logos onto existing uploaded images/videos (academy vs app)';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $dry = (bool) $this->option('dry-run');

        $stats = ['ok' => 0, 'skipped' => 0, 'failed' => 0, 'videos_need_ffmpeg' => 0];

        $diskRoots = config('watermark.scan.disk', []);
        foreach ($diskRoots as $root) {
            $this->scanDisk($root, $force, $dry, $stats);
        }

        foreach (config('watermark.scan.public_paths', []) as $publicRel) {
            $this->scanPublic($publicRel, $force, $dry, $stats);
        }

        $this->newLine();
        $this->info("Done. ok={$stats['ok']} skipped={$stats['skipped']} failed={$stats['failed']}");
        if ($stats['videos_need_ffmpeg'] > 0) {
            $this->warn("{$stats['videos_need_ffmpeg']} video(s) need ffmpeg — install it and set FFMPEG_PATH, then re-run.");
        }

        return self::SUCCESS;
    }

    protected function scanDisk(string $root, bool $force, bool $dry, array &$stats): void
    {
        $disk = Storage::disk('public');
        if (! $disk->exists($root)) {
            return;
        }

        foreach ($disk->allFiles($root) as $relative) {
            if (str_ends_with($relative, '.wm')) {
                continue;
            }
            if (str_starts_with(basename($relative), 'wm_')) {
                continue;
            }

            $brand = MediaWatermark::brandForPath($relative);
            if ($brand === null || ! MediaWatermark::isSupportedMedia($relative)) {
                $stats['skipped']++;
                continue;
            }

            $this->line(($dry ? '[dry] ' : '')."{$brand}: {$relative}");

            if ($dry) {
                $stats['ok']++;
                continue;
            }

            $result = MediaWatermark::applyDiskPath($relative, 'public', $force);
            $this->tally($result, $stats);
        }
    }

    protected function scanPublic(string $relativeDir, bool $force, bool $dry, array &$stats): void
    {
        $absDir = public_path($relativeDir);
        if (! is_dir($absDir)) {
            return;
        }

        foreach (File::allFiles($absDir) as $file) {
            $absolute = $file->getPathname();
            if (str_ends_with($absolute, '.wm')) {
                continue;
            }

            $relative = str_replace('\\', '/', $relativeDir.'/'.$file->getRelativePathname());
            $brand = MediaWatermark::brandForPath($relative) ?? MediaWatermark::BRAND_APP;

            if (! MediaWatermark::isSupportedMedia($absolute)) {
                $stats['skipped']++;
                continue;
            }

            $this->line(($dry ? '[dry] ' : '')."{$brand}: public/{$relative}");

            if ($dry) {
                $stats['ok']++;
                continue;
            }

            $result = MediaWatermark::applyAbsolute($absolute, $brand, $force);
            $this->tally($result, $stats);
        }
    }

    protected function tally(array $result, array &$stats): void
    {
        if (! empty($result['skipped'])) {
            $stats['skipped']++;

            return;
        }

        if (! empty($result['ok'])) {
            $stats['ok']++;

            return;
        }

        $stats['failed']++;
        $reason = (string) ($result['reason'] ?? 'error');
        if (str_contains($reason, 'ffmpeg')) {
            $stats['videos_need_ffmpeg']++;
        }
        $this->error('  failed: '.$reason);
    }
}
