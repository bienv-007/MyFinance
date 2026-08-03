<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'utilisateurs';

    protected $primaryKey = 'id_utilisateur';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'date_creation',
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    protected function casts(): array
    {
        return [
            'date_creation' => 'datetime',
            'mot_de_passe' => 'hashed',
        ];
    }

    public function getAuthPassword(): string
    {
        return $this->mot_de_passe;
    }
}
