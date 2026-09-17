<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->role, ['admin', 'organizador'], true);
    }

    public function rules(): array
    {
        return [
            'rodada' => ['required', 'integer', 'between:1,38'],
            'time_mandante_id' => ['required', 'integer', 'exists:times,id'],
            'time_visitante_id' => ['required', 'integer', 'exists:times,id', 'different:time_mandante_id'],
            'data_jogo' => ['nullable', 'date'],
        ];
    }
}
