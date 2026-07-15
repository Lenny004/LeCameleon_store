<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Storefront contact form submission for admin inbox.
 */
class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->update(['read_at' => now()]);
        }
    }
}
