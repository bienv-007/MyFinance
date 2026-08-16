<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    use HasFactory;

    protected $table = 'budgets';

    protected $primaryKey = 'id_budget';

    public $timestamps = false;

    protected $fillable = [
        'id_utilisateur',
        'periode',
        'montant_total',
        'solde',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'montant_total' => 'decimal:2',
        'solde' => 'decimal:2',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_budget';
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(BudgetHistorique::class, 'id_budget', 'id_budget');
    }

    public function getStatutAttribute(): string
    {
        $today = today();

        if ($this->date_debut->isAfter($today)) {
            return 'À venir';
        }

        if ($this->date_fin->isBefore($today)) {
            return 'Terminé';
        }

        return 'En cours';
    }
}
