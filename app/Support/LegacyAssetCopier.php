<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Copies demo assets from legacy/LeCameleon into public disk or public/images.
 */
class LegacyAssetCopier
{
    public static function basePath(): string
    {
        return base_path('legacy/LeCameleon');
    }

    public static function sourceExists(string $relative): bool
    {
        return is_file(self::resolveSource($relative));
    }

    public static function resolveSource(string $relative): string
    {
        return self::basePath().'/'.ltrim(str_replace('\\', '/', $relative), '/');
    }

    /**
     * Copy a legacy-relative file into storage/app/public.
     */
    public static function copyToPublicDisk(string $legacyRelative, string $destPath): ?string
    {
        $source = self::resolveSource($legacyRelative);

        if (! is_file($source)) {
            return null;
        }

        $destPath = ltrim(str_replace('\\', '/', $destPath), '/');
        Storage::disk('public')->makeDirectory(dirname($destPath));
        Storage::disk('public')->put($destPath, (string) file_get_contents($source));

        return $destPath;
    }

    /**
     * Copy a legacy-relative file into public/images (Vite-friendly static path).
     */
    public static function copyToPublicImages(string $legacyRelative, string $destRelative): ?string
    {
        $source = self::resolveSource($legacyRelative);

        if (! is_file($source)) {
            return null;
        }

        $destRelative = ltrim(str_replace('\\', '/', $destRelative), '/');
        $dest = public_path('images/'.$destRelative);
        File::ensureDirectoryExists(dirname($dest));
        File::copy($source, $dest);

        return 'images/'.$destRelative;
    }

    /**
     * Copy an absolute source file into storage/app/public.
     */
    public static function copyAbsoluteToPublicDisk(string $absoluteSource, string $destPath): ?string
    {
        if (! is_file($absoluteSource)) {
            return null;
        }

        $destPath = ltrim(str_replace('\\', '/', $destPath), '/');
        Storage::disk('public')->makeDirectory(dirname($destPath));
        Storage::disk('public')->put($destPath, (string) file_get_contents($absoluteSource));

        return $destPath;
    }

    /**
     * @return list<string> Absolute paths of files in a legacy directory.
     */
    public static function listFiles(string $relativeDir, array $extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif']): array
    {
        $dir = self::resolveSource($relativeDir);

        if (! is_dir($dir)) {
            return [];
        }

        $files = [];

        foreach ($extensions as $extension) {
            foreach (glob($dir.'/*.'.$extension) ?: [] as $file) {
                if (is_file($file)) {
                    $files[] = $file;
                }
            }

            foreach (glob($dir.'/*.'.strtoupper($extension)) ?: [] as $file) {
                if (is_file($file)) {
                    $files[] = $file;
                }
            }
        }

        $files = array_values(array_unique($files));
        sort($files);

        return $files;
    }
}
