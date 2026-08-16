<?php

namespace App\Actions;

use App\Models\Revenu;
use App\Models\RevenuPrevision;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class MarkRevenuPrevisionAsReceived
{
    public function __construct(private readonly NotificationService $notifications) {}

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

            $revenu = Revenu::create([
                'id_utilisateur' => $prevision->id_utilisateur,
                'montant' => $prevision->montant_previsionnel,
                'source' => $prevision->source_previsionnelle,
                'date_revenu' => $prevision->date_previsionnelle->toDateString(),
                'description' => $prevision->description,
            ]);

            $this->notifications->createOnce(
                $prevision->id_utilisateur,
                'revenu_prevision_percu',
                'Revenu prévu perçu',
                sprintf('Le revenu prévu « %s » a été marqué comme perçu.', $prevision->source_previsionnelle),
            );

            return $revenu;
        });
    }
}
