<?php

namespace App\Models;

use App\Enums\AddressType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User shipping or billing address.
 */
class Address extends Model
{
    use HasUuids;

    protected static function booted(): void
    {
        static::creating(function (self $address): void {
            $address->country = $address->country ?: 'SV';
        });
    }

    protected $fillable = [
        'user_id',
        'type',
        'label',
        'first_name',
        'last_name',
        'company',
        'line1',
        'line2',
        'city',
        'state',
        'postal_code',
        'country',
        'sv_municipality_id',
        'phone',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'type' => AddressType::class,
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function municipality(): BelongsTo
    {
        return $this->belongsTo(SvMunicipality::class, 'sv_municipality_id');
    }
}
