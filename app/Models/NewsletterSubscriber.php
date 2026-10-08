<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Footer newsletter capture (no external ESP yet).
 */
class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'token',
        'subscribed_at',
        'confirmed_at',
        'unsubscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'subscribed_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }
}
