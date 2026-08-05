<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepensePrevisionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_depense_prevision' => $this->id_depense_prevision,
            'id_utilisateur' => $this->id_utilisateur,
            'id_categorie' => $this->id_categorie,
            'montant_previsionnel' => $this->montant_previsionnel,
            'date_previsionnelle' => $this->date_previsionnelle,
            'description' => $this->description,
            'categorie' => new CategoryResource($this->whenLoaded('categorie')),
        ];
    }
}
