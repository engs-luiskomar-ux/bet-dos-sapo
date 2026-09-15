<?php

namespace App\Http\Requests;

use App\Enums\PartidaStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FiltroPartidaRequest extends FormRequest
{
    /** Somente usuario autenticado consulta a lista de partidas. */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'rodada' => ['nullable', 'integer', 'between:1,38'],
            'status' => ['nullable', Rule::enum(PartidaStatus::class)],
        ];
    }

    /** Campo vazio no formulario ("Todos") chega como "" e deve virar null. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'rodada' => $this->input('rodada') !== '' ? $this->input('rodada') : null,
            'status' => $this->input('status') !== '' ? $this->input('status') : null,
        ]);
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'rodada.integer' => 'A rodada deve ser um numero.',
            'rodada.between' => 'A rodada deve estar entre 1 e 38.',
            'status.enum' => 'Selecione um status valido.',
        ];
    }
}