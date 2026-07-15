<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Shipping zone anchored to a municipality for A→B rate matrix lookups.
 */
class ShippingZone extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'sv_municipality_id',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(SvMunicipality::class, 'sv_municipality_id');
    }

    public function originRates(): HasMany
    {
        return $this->hasMany(ShippingZoneRate::class, 'origin_zone_id');
    }

    public function destinationRates(): HasMany
    {
        return $this->hasMany(ShippingZoneRate::class, 'destination_zone_id');
    }
}
