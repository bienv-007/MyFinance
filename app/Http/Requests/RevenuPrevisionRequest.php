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
            'montant_previsionnel' => ['required', 'numeric', 'gt:0'],
            'source_previsionnelle' => ['required', 'string', 'max:255'],
            'date_previsionnelle' => ['required', 'date'],
            'description' => ['required', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'montant_previsionnel' => $this->input('montant_previsionnel', $this->input('montant_prevision')),
            'source_previsionnelle' => $this->input('source_previsionnelle', $this->input('source_prevision')),
            'date_previsionnelle' => $this->input('date_previsionnelle', $this->input('date_prevision')),
        ]);
    }

    public function messages(): array
    {
        return [
            'montant_previsionnel.required' => 'Le montant prévu est obligatoire.',
            'montant_previsionnel.numeric' => 'Le montant prévu doit être numérique.',
            'montant_previsionnel.gt' => 'Le montant prévu doit être supérieur à zéro.',
            'source_previsionnelle.required' => 'La source prévue est obligatoire.',
            'source_previsionnelle.max' => 'La source prévue ne peut pas dépasser 255 caractères.',
            'date_previsionnelle.required' => 'La date prévue est obligatoire.',
            'date_previsionnelle.date' => 'La date prévue est invalide.',
            'description.required' => 'La description est obligatoire.',
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
        ];
    }

    public function attributes(): array
    {
        return [
            'montant_previsionnel' => 'montant prévu',
            'source_previsionnelle' => 'source prévue',
            'date_previsionnelle' => 'date prévue',
            'description' => 'description',
        ];
    }
}
