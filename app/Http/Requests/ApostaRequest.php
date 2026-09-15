<?php

namespace App\Http\Requests;

use App\Models\Aposta;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ApostaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'torcedor';
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
