<?php

namespace App\Models;

use App\Enums\WarningAppliesTo;
use App\Enums\WarningSeverity;
use Illuminate\Database\Eloquent\Model;

/**
 * Customer-facing or admin delivery notice shown at checkout or tracking.
 */
class DeliveryWarning extends Model
{
    protected $fillable = [
        'code',
        'title',
        'body',
        'severity',
        'applies_to',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'severity' => WarningSeverity::class,
            'applies_to' => WarningAppliesTo::class,
            'is_active' => 'boolean',
        ];
    }
}
