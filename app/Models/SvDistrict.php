<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * District within a consolidated municipality (former standalone municipality).
 */
class SvDistrict extends Model
{
    protected $fillable = [
        'sv_municipality_id',
        'name',
        'slug',
        'is_active',
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
}
