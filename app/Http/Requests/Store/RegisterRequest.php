<?php

namespace App\Http\Requests\Store;

use App\Http\Requests\Concerns\NormalizesEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    use NormalizesEmail;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', 'max:255', Password::defaults()],
            'password_confirmation' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Keep account-existence details out of registration responses.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'No pudimos crear la cuenta con este correo. Si ya tienes una cuenta, inicia sesión.',
        ];
    }
}
