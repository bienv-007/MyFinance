<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_utilisateur' => $this->id_utilisateur,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'date_creation' => $this->date_creation,
            'notification_preferences' => [
                'notif_son' => $this->notificationPreference?->notif_son ?? true,
                'notif_vibration' => $this->notificationPreference?->notif_vibration ?? true,
                'notif_navigateur' => $this->notificationPreference?->notif_navigateur ?? false,
            ],
        ];
    }
}
