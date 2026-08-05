<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepensePrevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_categorie' => ['required', 'exists:categories,id_categorie'],
            'montant_previsionnel' => ['required', 'numeric', 'min:0'],
            'date_previsionnelle' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
