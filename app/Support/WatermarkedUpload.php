<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WatermarkedUpload
{
    /**
     * Store an uploaded file on a disk and watermark when applicable.
     */
    public static function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $path = $file->store($directory, $disk);
        MediaWatermark::applyDiskPath($path, $disk);

        return $path;
    }

    /**
     * Move an uploaded file into public_path($directory) and watermark.
     */
    public static function movePublic(UploadedFile $file, string $directoryUnderPublic, string $filename): string
    {
        $directoryUnderPublic = trim(str_replace('\\', '/', $directoryUnderPublic), '/');
        $destDir = public_path($directoryUnderPublic);
        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $file->move($destDir, $filename);
        $relative = $directoryUnderPublic.'/'.$filename;
        MediaWatermark::applyPublicRelative($relative);

        return $filename;
    }

    /**
     * Watermark an already-stored public-disk path (noop if skipped).
     */
    public static function afterStore(string $path, string $disk = 'public'): string
    {
        MediaWatermark::applyDiskPath($path, $disk);

        return $path;
    }
}
