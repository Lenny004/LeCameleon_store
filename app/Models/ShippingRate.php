<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Origin → destination shipping fee within a zone.
 */
class ShippingRate extends Model
{
    protected $fillable = [
        'shipping_zone_id',
        'origin_municipality_id',
        'destination_municipality_id',
        'fee',
    ];

    protected function casts(): array
    {
        return [
            'fee' => 'decimal:2',
        ];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function originMunicipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'origin_municipality_id');
    }

    public function destinationMunicipality(): BelongsTo
    {
        return $this->belongsTo(Municipality::class, 'destination_municipality_id');
    }
}
