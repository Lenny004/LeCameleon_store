<?php

namespace App\Models;

use App\Enums\OfferStatus;
use Database\Factories\OfferFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Buyer price proposal on a catalog product.
 */
class Offer extends Model
{
    /** @use HasFactory<OfferFactory> */
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'user_id',
        'amount',
        'message',
        'status',
        'counter_amount',
        'admin_notes',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'counter_amount' => 'decimal:2',
            'status' => OfferStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === OfferStatus::Pending;
    }
}
