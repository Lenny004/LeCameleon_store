<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Logistics carrier or delivery partner company.
 */
class LogisticsCompany extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'legal_name',
        'trade_name',
        'tax_id',
        'email',
        'phone',
        'address_line',
        'sv_municipality_id',
        'website',
        'logo_path',
        'contact_person',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(SvMunicipality::class, 'sv_municipality_id');
    }

    public function workers(): HasMany
    {
        return $this->hasMany(LogisticsWorker::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(LogisticsVehicle::class);
    }
}
