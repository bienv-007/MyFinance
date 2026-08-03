<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_depense' => $this->id_depense,
            'id_utilisateur' => $this->id_utilisateur,
            'id_categorie' => $this->id_categorie,
            'montant' => $this->montant,
            'date_depense' => $this->date_depense,
            'description' => $this->description,
            'categorie' => new CategoryResource($this->whenLoaded('categorie')),
        ];
    }
}
