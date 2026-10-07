<?php

namespace App\Http\Requests\Store;

use App\Http\Requests\Concerns\NormalizesEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    use NormalizesEmail;

    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->can('updateProfile', $user);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user())],
            'phone' => ['nullable', 'string', 'max:30'],
        ];
    }
}
