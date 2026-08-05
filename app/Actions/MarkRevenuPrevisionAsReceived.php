<?php

namespace App\Actions;

use App\Models\Revenu;
use App\Models\RevenuPrevision;
use Illuminate\Support\Facades\DB;

class MarkRevenuPrevisionAsReceived
{
    public function execute(RevenuPrevision $prevision): Revenu
    {
        return DB::transaction(function () use ($prevision): Revenu {
            $revenu = Revenu::query()
                ->where('id_utilisateur', $prevision->id_utilisateur)
                ->where('montant', $prevision->montant_previsionnel)
                ->where('source', $prevision->source_previsionnelle)
                ->whereDate('date_revenu', $prevision->date_previsionnelle)
                ->first();

            if ($revenu) {
                return $revenu;
            }

            return Revenu::create([
                'id_utilisateur' => $prevision->id_utilisateur,
                'montant' => $prevision->montant_previsionnel,
                'source' => $prevision->source_previsionnelle,
                'date_revenu' => $prevision->date_previsionnelle->toDateString(),
                'description' => $prevision->description,
            ]);
        });
    }
}
