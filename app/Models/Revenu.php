<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Revenu extends Model
{
    use HasFactory;

    protected $table = 'revenus';

    protected $primaryKey = 'id_revenu';

    protected $fillable = [
        'id_utilisateur',
        'id_budget',
        'id_budget_historique',
        'montant',
        'source',
        'date_revenu',
        'description',
    ];

    protected $casts = [
        'date_revenu' => 'date',
        'montant' => 'decimal:2',
    ];

    public $timestamps = false;

    public function getRouteKeyName(): string
    {
        return 'id_revenu';
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }
}
