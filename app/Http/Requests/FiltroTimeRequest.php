<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FiltroTimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'busca' => $this->busca
                ? trim($this->busca)
                : null,

            'estado' => $this->estado
                ? strtoupper(trim($this->estado))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'busca' => [
                'nullable',
                'string',
                'max:100',
            ],

            'estado' => [
                'nullable',
                'string',
                'size:2',
            ],
        ];
    }
}