<?php

namespace App\Models;

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
}
