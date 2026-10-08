<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Product gallery image.
 */
class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'path',
        'alt',
        'sort_order',
        'is_primary',
        'width',
        'height',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Public URL for this image, with placeholder fallback.
     */
    public function url(): string
    {
        $path = $this->path;

        if ($path === null || $path === '') {
            return static::placeholderUrl();
        }

        if ($this->isAbsoluteUrl($path)) {
            return $path;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return static::placeholderUrl();
    }

    public function thumbUrl(): string
    {
        $path = $this->thumbPath();

        if ($path !== null && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return $this->url();
    }

    public function srcset(): string
    {
        $original = $this->url();
        $thumb = $this->thumbUrl();
        [$width, $height] = $this->dimensions();

        if ($width < 1) {
            return $original;
        }

        if ($thumb === $original) {
            return $original;
        }

        $maxSide = (int) config('store.product_thumbnail_max_side', 400);
        $ratio = min($maxSide / max($width, 1), $maxSide / max($height, 1), 1);
        $thumbWidth = max(1, (int) round($width * $ratio));

        return sprintf('%s %dw, %s %dw', $thumb, max(1, $thumbWidth), $original, $width);
    }

    public function thumbPath(): ?string
    {
        if (! is_string($this->path) || $this->path === '' || $this->isAbsoluteUrl($this->path)) {
            return null;
        }

        $pathInfo = pathinfo($this->path);

        return ($pathInfo['dirname'] ?? '.').'/'.($pathInfo['filename'] ?? 'image').'_thumb.'.($pathInfo['extension'] ?? 'jpg');
    }

    /** @return array{0: int, 1: int} */
    public function dimensions(): array
    {
        if ($this->width && $this->height) {
            return [(int) $this->width, (int) $this->height];
        }

        return $this->readDimensions();
    }

    public static function placeholderUrl(): string
    {
        $path = (string) config('store.product_image_placeholder', 'placeholders/vintage-product.jpg');

        return Storage::disk('public')->url($path);
    }

    /**
     * Resolve a stored path (or model) to a public URL.
     */
    public static function urlFor(mixed $imageOrPath): string
    {
        if ($imageOrPath instanceof self) {
            return $imageOrPath->url();
        }

        if (is_string($imageOrPath) && $imageOrPath !== '') {
            return (new self(['path' => $imageOrPath]))->url();
        }

        return static::placeholderUrl();
    }

    private function isAbsoluteUrl(string $path): bool
    {
        return str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://')
            || str_starts_with($path, '/');
    }

    /** @return array{0: int, 1: int} */
    private function readDimensions(): array
    {
        if (! is_string($this->path) || $this->path === '' || $this->isAbsoluteUrl($this->path)) {
            return [0, 0];
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($this->path)) {
            return [0, 0];
        }

        $info = @getimagesize($disk->path($this->path));

        return $info === false ? [0, 0] : [(int) $info[0], (int) $info[1]];
    }
}
