<?php

namespace App\Http\Requests;

use App\Models\Aposta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ApostaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'torcedor';
    }


    public function rules(): array
    {
        return [
            'partida_id' => [
                'required',
                'integer',
                'exists:partidas,id',
            ],
            'palpite' => [
                'required',
                Rule::in(array_keys(Aposta::OPCOES)),
            ],
            'valor' => [
                'required',
                'integer',
                'min:10',
                'max:1000',
            ],
        ];
    }
}