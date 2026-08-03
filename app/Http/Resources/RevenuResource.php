<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RevenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_revenu' => $this->id_revenu,
            'id_utilisateur' => $this->id_utilisateur,
            'montant' => $this->montant,
            'source' => $this->source,
            'date_revenu' => $this->date_revenu,
            'description' => $this->description,
        ];
    }
}
