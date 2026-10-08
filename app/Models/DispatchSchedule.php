<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Next outbound dispatch window configured by operations.
 */
class DispatchSchedule extends Model
{
    protected $fillable = [
        'name',
        'next_dispatch_at',
        'cutoff_at',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'next_dispatch_at' => 'datetime',
            'cutoff_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /** @param  Builder<self>  $query */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
