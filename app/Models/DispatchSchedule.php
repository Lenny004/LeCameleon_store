<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Scheduled dispatch window for warehouse pickups.
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
}
