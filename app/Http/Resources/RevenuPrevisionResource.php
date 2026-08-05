<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RevenuPrevisionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_prevision_revenu' => $this->id_revenu_prevision,
            'id_revenu_prevision' => $this->id_revenu_prevision,
            'id_utilisateur' => $this->id_utilisateur,
            'montant_prevision' => $this->montant_previsionnel,
            'montant_previsionnel' => $this->montant_previsionnel,
            'source_prevision' => $this->source_previsionnelle,
            'source_previsionnelle' => $this->source_previsionnelle,
            'date_prevision' => $this->date_previsionnelle?->format('Y-m-d'),
            'date_previsionnelle' => $this->date_previsionnelle?->format('Y-m-d'),
            'description' => $this->description,
            'est_realisee' => $this->est_realisee,
            'statut' => $this->statut,
        ];
    }
}
