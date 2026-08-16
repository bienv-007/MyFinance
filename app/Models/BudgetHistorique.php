<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetHistorique extends Model
{
    protected $table = 'budget_historiques';
    protected $primaryKey = 'id_budget_historique';
    public $timestamps = false;
    protected $fillable = ['id_budget', 'id_utilisateur', 'periode', 'montant_total', 'solde_final', 'montant_depense', 'date_debut', 'date_fin', 'date_archivage'];
    protected $casts = ['montant_total' => 'decimal:2', 'solde_final' => 'decimal:2', 'montant_depense' => 'decimal:2', 'date_debut' => 'date', 'date_fin' => 'date', 'date_archivage' => 'datetime'];

    public function budget(): BelongsTo { return $this->belongsTo(Budget::class, 'id_budget', 'id_budget'); }

    public function depenses(): HasMany { return $this->hasMany(Depense::class, 'id_budget_historique', 'id_budget_historique'); }

    public function revenus(): HasMany { return $this->hasMany(Revenu::class, 'id_budget_historique', 'id_budget_historique'); }
}
