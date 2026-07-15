<?php

namespace App\Http\Requests\Admin;

use App\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStaff() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'logistics_company_id' => ['nullable', 'uuid', 'exists:logistics_companies,id'],
            'logistics_worker_id' => ['nullable', 'uuid', 'exists:logistics_workers,id'],
            'logistics_vehicle_id' => ['nullable', 'uuid', 'exists:logistics_vehicles,id'],
            'destination_municipality_id' => ['nullable', 'integer', 'exists:sv_municipalities,id'],
            'carrier' => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:150'],
            'status' => ['required', Rule::enum(ShipmentStatus::class)],
        ];
    }
}
