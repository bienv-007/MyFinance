<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_categorie' => ['required', 'exists:categories,id_categorie'],
            'montant' => ['required', 'numeric', 'min:0'],
            'date_depense' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
