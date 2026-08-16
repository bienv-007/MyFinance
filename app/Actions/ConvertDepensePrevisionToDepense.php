<?php

namespace App\Actions;

use App\Models\Budget;
use App\Models\Depense;
use App\Models\DepensePrevision;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConvertDepensePrevisionToDepense
{
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
                'id_categorie' => $prevision->id_categorie,
                'montant' => $prevision->montant_previsionnel,
                'date_depense' => $prevision->date_previsionnelle->toDateString(),
                'description' => $prevision->description,
            ]);

            $budget->decrement('solde', $prevision->montant_previsionnel);
            $prevision->delete();

            return $depense->load('categorie');
        });
    }
}
