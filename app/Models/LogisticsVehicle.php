<?php

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Delivery fleet vehicle assigned to a logistics company.
 */
class LogisticsVehicle extends Model
{
    use HasUuids;

    protected $fillable = [
        'logistics_company_id',
        'logistics_worker_id',
        'plate_number',
        'brand',
        'model',
        'year',
        'color',
        'vehicle_type',
        'capacity_kg',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'vehicle_type' => VehicleType::class,
            'capacity_kg' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(LogisticsCompany::class, 'logistics_company_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(LogisticsWorker::class, 'logistics_worker_id');
    }
}
