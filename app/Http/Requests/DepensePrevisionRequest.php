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
            'montant_previsionnel' => ['required', 'numeric', 'gt:0'],
            'date_previsionnelle' => ['required', 'date'],
            'description' => ['required', 'string', 'max:2000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'montant_previsionnel' => $this->input('montant_previsionnel', $this->input('montant_prevision')),
            'date_previsionnelle' => $this->input('date_previsionnelle', $this->input('date_prevision')),
        ]);
    }

    public function messages(): array
    {
        return [
            'id_categorie.required' => 'La catégorie est obligatoire.',
            'id_categorie.exists' => 'La catégorie sélectionnée est invalide.',
            'montant_previsionnel.required' => 'Le montant prévu est obligatoire.',
            'montant_previsionnel.numeric' => 'Le montant prévu doit être un nombre.',
            'montant_previsionnel.gt' => 'Le montant prévu doit être supérieur à zéro.',
            'date_previsionnelle.required' => 'La date prévue est obligatoire.',
            'date_previsionnelle.date' => 'La date prévue doit être valide.',
            'description.required' => 'La description est obligatoire.',
            'description.string' => 'La description doit être un texte.',
            'description.max' => 'La description ne peut pas dépasser :max caractères.',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_categorie' => 'catégorie',
            'montant_previsionnel' => 'montant prévu',
            'date_previsionnelle' => 'date prévue',
            'description' => 'description',
        ];
    }
}
