<?php

namespace App\Http\Requests\Store;

use App\Models\SavedSearch;
use Illuminate\Foundation\Http\FormRequest;

class SavedSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'query_params' => ['required', 'array'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $params = $this->input('query_params', []);

            if (! SavedSearch::filtersAreActive($params)) {
                $validator->errors()->add('query_params', 'La búsqueda debe incluir al menos un filtro activo.');
            }
        });
    }
}
