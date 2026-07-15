<?php

namespace App\Models;

use App\Enums\RelationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Product-to-product relation (related, upsell, cross-sell, similar).
 */
class ProductRelation extends Model
{
    protected $fillable = [
        'product_id',
        'related_product_id',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => RelationType::class,
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function relatedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'related_product_id');
    }
}
