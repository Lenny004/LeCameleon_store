<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Municipality with base shipping cost and geo coordinates.
 */
class Municipality extends Model
{
    protected $fillable = [
        'department_id',
        'code',
        'name',
        'name_es',
        'base_shipping_cost',
        'latitude',
        'longitude',
        'is_serviceable',
    ];

    protected function casts(): array
    {
        return [
            'base_shipping_cost' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_serviceable' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function logisticsCompanies(): HasMany
    {
        return $this->hasMany(LogisticsCompany::class);
    }

    public function displayName(): string
    {
        return $this->name_es ?: $this->name;
    }
}
