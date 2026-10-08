<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Customer bookmark for a filtered catalog query (email digests later).
 */
class SavedSearch extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'query_params',
        'last_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'query_params' => 'array',
            'last_notified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shopUrl(): string
    {
        return route('shop.index', $this->query_params ?? []);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public static function filtersAreActive(array $filters): bool
    {
        foreach ($filters as $key => $value) {
            if ($key === 'sort' && in_array($value, ['', 'newest', 'new'], true)) {
                continue;
            }

            if ($value !== null && $value !== '' && $value !== false) {
                return true;
            }
        }

        return false;
    }
}
