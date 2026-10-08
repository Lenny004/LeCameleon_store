<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class NewsletterRequest extends FormRequest
{
    /**
     * Bolsa de errores propia: el formulario vive en el footer y no debe
     * mezclarse con los errores de `email` de login/registro/perfil.
     */
    protected $errorBag = 'newsletter';

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
            'email' => ['required', 'email', 'max:255'],
        ];
    }
}
