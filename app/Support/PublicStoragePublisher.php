<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Store uploads on the public disk (and optionally mirror under public/storage).
 */
class PublicStoragePublisher
{
    public static function storeAndPublish(UploadedFile $file, string $directory, bool $watermark = true): string
    {
        $path = $file->store(trim($directory, '/'), 'public');

        if ($watermark) {
            MediaWatermark::applyDiskPath($path, 'public');
        }

        // Ensure the public/storage symlink target is reachable; no extra copy needed when linked.
        if (! Storage::disk('public')->exists($path)) {
            throw new \RuntimeException("Failed to store file at {$path}");
        }

        return $path;
    }
}
