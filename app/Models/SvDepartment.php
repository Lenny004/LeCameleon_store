<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * El Salvador department (14 total, post-2024 municipal reform).
 */
class SvDepartment extends Model
{
    protected $fillable = [
        'code',
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

    public function municipalities(): HasMany
    {
        return $this->hasMany(SvMunicipality::class);
    }
}
