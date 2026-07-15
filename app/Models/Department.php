<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * El Salvador department (read-only territorial grouping).
 */
class Department extends Model
{
    protected $fillable = [
        'code',
        'name',
        'name_es',
    ];

    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class);
    }
}
