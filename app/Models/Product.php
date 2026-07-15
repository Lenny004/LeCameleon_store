<?php

namespace App\Models;

use App\Enums\ConditionGrade;
use App\Enums\ProductStatus;
use App\Enums\ProductType;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Vintage catalog product — apparel, object, or accessory.
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'brand_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'type',
        'status',
        'description',
        'short_description',
        'price',
        'compare_at_price',
        'cost_price',
        'condition_grade',
        'era_decade',
        'size_label',
        'color',
        'material',
        'measurements',
        'is_authenticated',
        'authenticity_notes',
        'authenticated_at',
        'authenticated_by',
        'is_unique_piece',
        'quantity_available',
        'quantity_reserved',
        'low_stock_threshold',
        'meta_title',
        'meta_description',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductType::class,
            'status' => ProductStatus::class,
            'condition_grade' => ConditionGrade::class,
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'is_unique_piece' => 'boolean',
            'quantity_available' => 'integer',
            'quantity_reserved' => 'integer',
            'low_stock_threshold' => 'integer',
            'published_at' => 'datetime',
            'measurements' => 'array',
            'is_authenticated' => 'boolean',
            'authenticated_at' => 'datetime',
        ];
    }

    public function authenticator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'authenticated_by');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function relations(): HasMany
    {
        return $this->hasMany(ProductRelation::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ProductView::class);
    }

    public function stockAlerts(): HasMany
    {
        return $this->hasMany(StockAlert::class);
    }

    public function isInStock(): bool
    {
        return $this->quantity_available > $this->quantity_reserved;
    }

    public function isLowStock(): bool
    {
        $available = $this->quantity_available - $this->quantity_reserved;

        return $available > 0 && $available <= $this->low_stock_threshold;
    }
}
