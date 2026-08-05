<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_budget' => $this->id_budget,
            'id_utilisateur' => $this->id_utilisateur,
            'periode' => $this->periode,
            'montant_total' => $this->montant_total,
            'date_debut' => $this->date_debut?->format('Y-m-d'),
            'date_fin' => $this->date_fin?->format('Y-m-d'),
            'statut' => $this->statut,
        ];
    }
}
