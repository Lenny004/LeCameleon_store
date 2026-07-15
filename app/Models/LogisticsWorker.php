<?php

namespace App\Models;

use App\Enums\LogisticsWorkerRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Collector, driver, dispatcher, or supervisor on the delivery network.
 */
class LogisticsWorker extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'logistics_company_id',
        'user_id',
        'employee_code',
        'first_name',
        'last_name',
        'document_id',
        'phone',
        'email',
        'role',
        'hire_date',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'role' => LogisticsWorkerRole::class,
            'hire_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(LogisticsCompany::class, 'logistics_company_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedVehicles(): HasMany
    {
        return $this->hasMany(LogisticsVehicle::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
