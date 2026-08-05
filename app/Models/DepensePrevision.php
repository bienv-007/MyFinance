<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepensePrevision extends Model
{
    use HasFactory;

    protected $table = 'depense_previsions';

    protected $primaryKey = 'id_depense_prevision';

    public $timestamps = false;

    protected $fillable = [
        'id_utilisateur',
        'id_categorie',
        'montant_previsionnel',
        'date_previsionnelle',
        'description',
    ];

    protected $casts = [
        'montant_previsionnel' => 'decimal:2',
        'date_previsionnelle' => 'date',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_depense_prevision';
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'id_categorie', 'id_categorie');
    }

    public function getIdPrevisionAttribute(): mixed
    {
        return $this->id_depense_prevision;
    }

    public function getMontantPrevisionAttribute(): mixed
    {
        return $this->montant_previsionnel;
    }

    public function getDatePrevisionAttribute(): mixed
    {
        return $this->date_previsionnelle;
    }

    public function getStatutAttribute(): string
    {
        $today = today();

        if ($this->date_previsionnelle->isSameDay($today)) {
            return "Aujourd'hui";
        }

        if ($this->date_previsionnelle->isAfter($today)) {
            return 'À venir';
        }

        return 'Dépassée';
    }
}
