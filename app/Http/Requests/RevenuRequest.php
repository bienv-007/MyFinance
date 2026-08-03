<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'montant' => ['required', 'numeric', 'min:0'],
            'source' => ['required', 'string', 'max:255'],
            'date_revenu' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ];
    }
}
