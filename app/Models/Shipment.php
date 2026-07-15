<?php

namespace App\Models;

use App\Enums\ShipmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Order fulfillment shipment tracking.
 */
class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'logistics_company_id',
        'logistics_worker_id',
        'logistics_vehicle_id',
        'origin_municipality_id',
        'destination_municipality_id',
        'shipping_zone_rate_id',
        'quoted_fee',
        'distance_km',
        'carrier',
        'tracking_number',
        'status',
        'recipient_name',
        'recipient_phone',
        'recipient_notes',
        'shipped_at',
        'delivered_at',
        'next_attempt_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ShipmentStatus::class,
            'quoted_fee' => 'decimal:2',
            'distance_km' => 'decimal:2',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
            'next_attempt_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(LogisticsCompany::class, 'logistics_company_id');
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(LogisticsWorker::class, 'logistics_worker_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(LogisticsVehicle::class, 'logistics_vehicle_id');
    }

    public function originMunicipality(): BelongsTo
    {
        return $this->belongsTo(SvMunicipality::class, 'origin_municipality_id');
    }

    public function destinationMunicipality(): BelongsTo
    {
        return $this->belongsTo(SvMunicipality::class, 'destination_municipality_id');
    }

    public function zoneRate(): BelongsTo
    {
        return $this->belongsTo(ShippingZoneRate::class, 'shipping_zone_rate_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class)->orderBy('happened_at');
    }
}
