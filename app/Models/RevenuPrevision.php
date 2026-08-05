<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RevenuPrevision extends Model
{
    use HasFactory;

    protected $table = 'revenu_previsions';

    protected $primaryKey = 'id_revenu_prevision';

    public $timestamps = false;

    protected $fillable = [
        'id_utilisateur',
        'montant_previsionnel',
        'source_previsionnelle',
        'date_previsionnelle',
        'description',
    ];

    protected $casts = [
        'montant_previsionnel' => 'decimal:2',
        'date_previsionnelle' => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_revenu_prevision';
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function scopeWithRealizedStatus(Builder $query): Builder
    {
        return $query->addSelect([
            'is_realisee' => Revenu::query()
                ->selectRaw('1')
                ->whereColumn('revenus.id_utilisateur', 'revenu_previsions.id_utilisateur')
                ->whereColumn('revenus.montant', 'revenu_previsions.montant_previsionnel')
                ->whereColumn('revenus.source', 'revenu_previsions.source_previsionnelle')
                ->whereColumn('revenus.date_revenu', 'revenu_previsions.date_previsionnelle')
                ->limit(1),
        ]);
    }

    public function scopeUnrealized(Builder $query): Builder
    {
        return $query->whereNotExists(
            Revenu::query()
                ->selectRaw('1')
                ->whereColumn('revenus.id_utilisateur', 'revenu_previsions.id_utilisateur')
                ->whereColumn('revenus.montant', 'revenu_previsions.montant_previsionnel')
                ->whereColumn('revenus.source', 'revenu_previsions.source_previsionnelle')
                ->whereColumn('revenus.date_revenu', 'revenu_previsions.date_previsionnelle')
                ->limit(1),
        );
    }

    public function getIdPrevisionRevenuAttribute(): mixed
    {
        return $this->id_revenu_prevision;
    }

    public function getMontantPrevisionAttribute(): mixed
    {
        return $this->montant_previsionnel;
    }

    public function getSourcePrevisionAttribute(): mixed
    {
        return $this->source_previsionnelle;
    }

    public function getDatePrevisionAttribute(): mixed
    {
        return $this->date_previsionnelle;
    }

    public function getEstRealiseeAttribute(): bool
    {
        if (array_key_exists('is_realisee', $this->attributes)) {
            return (bool) $this->attributes['is_realisee'];
        }

        return Revenu::query()
            ->where('id_utilisateur', $this->id_utilisateur)
            ->where('montant', $this->montant_previsionnel)
            ->where('source', $this->source_previsionnelle)
            ->whereDate('date_revenu', $this->date_previsionnelle)
            ->exists();
    }

    public function getStatutAttribute(): string
    {
        if ($this->est_realisee) {
            return 'Réalisée';
        }

        $today = today();

        if ($this->date_previsionnelle->isSameDay($today)) {
            return "Aujourd'hui";
        }

        if ($this->date_previsionnelle->isAfter($today)) {
            return 'À venir';
        }

        return 'Expirée';
    }
}
