<?php

namespace App\Http\Requests\Admin;

use App\Enums\RecipientOutcome;
use App\Enums\ShipmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShipmentEventRequest extends FormRequest
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
            'status' => ['required', Rule::enum(ShipmentStatus::class)],
            'recipient_outcome' => ['nullable', Rule::enum(RecipientOutcome::class)],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
}
