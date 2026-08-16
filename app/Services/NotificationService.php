<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function createOnce(int $userId, string $type, string $title, string $content): Notification
    {
        return Notification::query()->firstOrCreate([
            'id_utilisateur' => $userId,
            'type' => $type,
            'titre' => $title,
            'contenu' => $content,
        ]);
    }
}
