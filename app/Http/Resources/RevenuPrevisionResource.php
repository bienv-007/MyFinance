<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RevenuPrevisionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_revenu_prevision' => $this->id_revenu_prevision,
            'id_utilisateur' => $this->id_utilisateur,
            'montant_previsionnel' => $this->montant_previsionnel,
            'source_previsionnelle' => $this->source_previsionnelle,
            'date_previsionnelle' => $this->date_previsionnelle,
            'description' => $this->description,
        ];
    }
}
