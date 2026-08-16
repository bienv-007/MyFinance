<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $primaryKey = 'id_notification';

    public $timestamps = false;

    protected $fillable = ['id_utilisateur', 'type', 'titre', 'contenu', 'est_lue', 'date_notification'];

    protected $casts = ['est_lue' => 'boolean', 'date_notification' => 'datetime'];

    public function getRouteKeyName(): string
    {
        return 'id_notification';
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }
}
