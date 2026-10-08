<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Shopping cart for authenticated users or guest sessions.
 */
class Cart extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'session_id',
        'reminded_at',
    ];

    protected function casts(): array
    {
        return [
            'reminded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
