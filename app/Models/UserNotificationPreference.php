<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    protected $table = 'user_notification_preferences';

    protected $fillable = [
        'id_utilisateur',
        'notif_son',
        'notif_vibration',
        'notif_navigateur',
    ];

    protected function casts(): array
    {
        return [
            'notif_son' => 'boolean',
            'notif_vibration' => 'boolean',
            'notif_navigateur' => 'boolean',
        ];
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }
}
