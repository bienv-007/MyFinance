<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'periode' => ['required', 'string', 'max:255'],
            'montant_total' => ['required', 'numeric', 'gt:0'],
            'reinitialiser_solde' => ['sometimes', 'boolean'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after:date_debut'],
        ];
    }

    public function messages(): array
    {
        return [
            'periode.required' => 'La période est obligatoire.',
            'periode.string' => 'La période doit être une chaîne de caractères.',
            'periode.max' => 'La période ne peut pas dépasser :max caractères.',
            'montant_total.required' => 'Le montant total est obligatoire.',
            'montant_total.numeric' => 'Le montant total doit être un nombre.',
            'montant_total.gt' => 'Le montant total doit être supérieur à zéro.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_debut.date' => 'La date de début doit être une date valide.',
            'date_fin.required' => 'La date de fin est obligatoire.',
            'date_fin.date' => 'La date de fin doit être une date valide.',
            'date_fin.after' => 'La date de fin doit être postérieure à la date de début.',
        ];
    }

    public function attributes(): array
    {
        return [
            'periode' => 'période',
            'montant_total' => 'montant total',
            'date_debut' => 'date de début',
            'date_fin' => 'date de fin',
        ];
    }
}
