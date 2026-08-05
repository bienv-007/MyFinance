<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepensePrevisionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_prevision' => $this->id_depense_prevision,
            'id_depense_prevision' => $this->id_depense_prevision,
            'id_utilisateur' => $this->id_utilisateur,
            'id_categorie' => $this->id_categorie,
            'montant_prevision' => $this->montant_previsionnel,
            'montant_previsionnel' => $this->montant_previsionnel,
            'date_prevision' => $this->date_previsionnelle?->format('Y-m-d'),
            'date_previsionnelle' => $this->date_previsionnelle?->format('Y-m-d'),
            'description' => $this->description,
            'statut' => $this->statut,
            'categorie' => new CategoryResource($this->whenLoaded('categorie')),
        ];
    }
}
