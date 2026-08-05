<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevenuPrevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'montant_previsionnel' => ['required', 'numeric', 'min:0'],
            'source_previsionnelle' => ['required', 'string', 'max:255'],
            'date_previsionnelle' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
