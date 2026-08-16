<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_notification' => $this->id_notification,
            'type' => $this->type,
            'titre' => $this->titre,
            'contenu' => $this->contenu,
            'est_lue' => $this->est_lue,
            'date_notification' => $this->date_notification?->toISOString(),
        ];
    }
}
