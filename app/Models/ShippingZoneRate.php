<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Origin-to-destination shipping rate between two zones.
 */
class ShippingZoneRate extends Model
{
    protected $fillable = [
        'origin_zone_id',
        'destination_zone_id',
        'base_fee',
        'per_km_fee',
        'min_fee',
        'max_fee',
        'estimated_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_fee' => 'decimal:2',
            'per_km_fee' => 'decimal:2',
            'min_fee' => 'decimal:2',
            'max_fee' => 'decimal:2',
            'estimated_hours' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function originZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'origin_zone_id');
    }

    public function destinationZone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'destination_zone_id');
    }
}
