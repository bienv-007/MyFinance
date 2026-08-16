<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Depense extends Model
{
    use HasFactory;

    protected $table = 'depenses';

    protected $primaryKey = 'id_depense';

    protected $fillable = [
        'id_utilisateur',
        'id_budget',
        'id_budget_historique',
        'id_categorie',
        'montant',
        'date_depense',
        'description',
    ];

    protected $casts = [
        'date_depense' => 'date',
        'montant' => 'decimal:2',
    ];

    public $timestamps = false;

    public function getRouteKeyName(): string
    {
        return 'id_depense';
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }
}
