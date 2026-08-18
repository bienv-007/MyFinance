<?php

namespace App\Actions;

use App\Models\Budget;
use App\Models\Depense;
use App\Models\DepensePrevision;
use App\Services\BudgetCycleService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConvertDepensePrevisionToDepense
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly BudgetCycleService $cycles,
    ) {}

    public function execute(DepensePrevision $prevision): Depense
    {
        return DB::transaction(function () use ($prevision): Depense {
            $budget = Budget::query()
                ->where('id_utilisateur', $prevision->id_utilisateur)
                ->whereDate('date_debut', '<=', $prevision->date_previsionnelle)
                ->whereDate('date_fin', '>=', $prevision->date_previsionnelle)
                ->lockForUpdate()
                ->first();

            if ($budget === null) {
                throw ValidationException::withMessages([
                    'date_previsionnelle' => 'Aucun budget ne couvre la date de cette prévision.',
                ]);
            }

            if ((float) $budget->solde < (float) $prevision->montant_previsionnel) {
                throw ValidationException::withMessages([
                    'montant_previsionnel' => 'Le solde disponible du budget est insuffisant pour valider cette prévision.',
                ]);
            }

            $depense = Depense::create([
                'id_utilisateur' => $prevision->id_utilisateur,
                'id_budget' => $budget->id_budget,
                'id_categorie' => $prevision->id_categorie,
                'montant' => $prevision->montant_previsionnel,
                'date_depense' => $prevision->date_previsionnelle->toDateString(),
                'description' => $prevision->description,
            ]);

            $budget->decrement('solde', $prevision->montant_previsionnel);
            $this->notifications->notifyBudgetUsageThresholds($budget->refresh());
            $this->cycles->archiveIfNecessary($budget);
            $prevision->delete();
            $this->notifications->createOnce(
                $prevision->id_utilisateur,
                'depense_prevision_validee',
                'Prévision de dépense validée',
                sprintf('La prévision « %s » a été convertie en dépense réelle.', $prevision->description),
            );

            return $depense->load('categorie');
        });
    }
}
