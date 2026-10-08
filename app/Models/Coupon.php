<?php

namespace App\Models;

use App\Enums\CouponType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Promotional discount coupon.
 */
class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_uses',
        'max_uses_per_user',
        'first_order_only',
        'used_count',
        'starts_at',
        'ends_at',
        'is_active',
        'shipping_only',
    ];

    protected function casts(): array
    {
        return [
            'type' => CouponType::class,
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_uses' => 'integer',
            'max_uses_per_user' => 'integer',
            'first_order_only' => 'boolean',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'shipping_only' => 'boolean',
        ];
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false;
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'coupon_category');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }
}
