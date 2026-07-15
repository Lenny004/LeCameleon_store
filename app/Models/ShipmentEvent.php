<?php

namespace App\Models;

use App\Enums\RecipientOutcome;
use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Timeline event for a shipment status change or delivery attempt.
 */
class ShipmentEvent extends Model
{
    protected $fillable = [
        'shipment_id',
        'status',
        'recipient_outcome',
        'note',
        'happened_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'recipient_outcome' => RecipientOutcome::class,
            'happened_at' => 'datetime',
        ];
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
