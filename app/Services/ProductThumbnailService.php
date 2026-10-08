<?php

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

/** Generates the optional public-disk thumbnail beside a product image. */
class ProductThumbnailService
{
    public function generate(ProductImage $image): bool
    {
        if (! extension_loaded('gd') || ! is_string($image->path) || $image->path === '') {
            return false;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($image->path)) {
            return false;
        }

        $sourcePath = $disk->path($image->path);
        $info = @getimagesize($sourcePath);
        if ($info === false) {
            return false;
        }

        [$width, $height, $type] = $info;
        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            default => false,
        };

        if ($source === false) {
            return false;
        }

        $max = (int) config('store.product_thumbnail_max_side', 400);
        $ratio = min($max / max($width, 1), $max / max($height, 1), 1);
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));
        $thumb = imagecreatetruecolor($newWidth, $newHeight);

        if ($thumb === false) {
            imagedestroy($source);

            return false;
        }

        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        $thumbPath = $image->thumbPath();
        $saved = $thumbPath === null ? false : match ($type) {
            IMAGETYPE_JPEG => imagejpeg($thumb, $disk->path($thumbPath), 85),
            IMAGETYPE_PNG => imagepng($thumb, $disk->path($thumbPath)),
            IMAGETYPE_WEBP => function_exists('imagewebp') ? imagewebp($thumb, $disk->path($thumbPath), 85) : false,
            default => false,
        };

        imagedestroy($source);
        imagedestroy($thumb);

        if ($saved === false && $thumbPath !== null) {
            @unlink($disk->path($thumbPath));
        }

        return $saved !== false;
    }

    /** @return array{width: int, height: int}|null */
    public function dimensions(ProductImage $image): ?array
    {
        if (! is_string($image->path) || $image->path === '') {
            return null;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($image->path)) {
            return null;
        }

        $info = @getimagesize($disk->path($image->path));

        return $info === false ? null : ['width' => (int) $info[0], 'height' => (int) $info[1]];
    }
}
