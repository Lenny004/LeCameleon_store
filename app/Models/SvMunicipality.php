<?php

namespace App\Models;

use App\Enums\MunicipalityRegion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Consolidated El Salvador municipality with flat warehouse shipping rate.
 */
class SvMunicipality extends Model
{
    protected $fillable = [
        'sv_department_id',
        'code',
        'name',
        'slug',
        'region_label',
        'base_shipping_cost',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'region_label' => MunicipalityRegion::class,
            'base_shipping_cost' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(SvDepartment::class, 'sv_department_id');
    }

    public function districts(): HasMany
    {
        return $this->hasMany(SvDistrict::class);
    }

    public function shippingZones(): HasMany
    {
        return $this->hasMany(ShippingZone::class);
    }
}
