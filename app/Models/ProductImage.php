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
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
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
}
