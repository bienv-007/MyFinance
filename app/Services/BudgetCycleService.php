<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetHistorique;
use App\Models\Depense;
use App\Models\Revenu;
use Illuminate\Support\Facades\DB;

class BudgetCycleService
{
    public function archiveCompletedAndExpiredBudgets(): void
    {
        Budget::query()
            ->where('est_archive', false)
            ->where(function ($query): void {
                $query->where('solde', '<=', 0)
                    ->orWhereDate('date_fin', '<', today());
            })
            ->each(fn (Budget $budget) => $this->archive($budget));
    }

    public function archiveIfNecessary(Budget $budget): void
    {
        if (! $budget->est_archive && ((float) $budget->solde <= 0 || $budget->date_fin->isBefore(today()))) {
            $this->archive($budget);
        }
    }

    public function archive(Budget $budget): BudgetHistorique
    {
        return DB::transaction(function () use ($budget): BudgetHistorique {
            $budget->refresh();

            $historique = $budget->historiques()->latest('id_budget_historique')->first();

            if ($budget->est_archive && $historique !== null) {
                return $historique;
            }

            $depenses = Depense::query()
                ->where('id_utilisateur', $budget->id_utilisateur)
                ->whereNull('id_budget_historique')
                ->get();
            $revenus = Revenu::query()
                ->where('id_utilisateur', $budget->id_utilisateur)
                ->whereNull('id_budget_historique')
                ->get();
            $historique = BudgetHistorique::create([
                'id_budget' => $budget->id_budget,
                'id_utilisateur' => $budget->id_utilisateur,
                'periode' => $budget->periode,
                'montant_total' => $budget->montant_total,
                'solde_final' => $budget->solde,
                'montant_depense' => $depenses->sum('montant'),
                'date_debut' => $budget->date_debut,
                'date_fin' => $budget->date_fin,
            ]);

            Depense::query()->whereKey($depenses->pluck('id_depense'))->update(['id_budget_historique' => $historique->id_budget_historique]);
            Revenu::query()->whereKey($revenus->pluck('id_revenu'))->update(['id_budget_historique' => $historique->id_budget_historique]);
            $budget->update(['est_archive' => true]);

            return $historique;
        });
    }

    public function restart(Budget $budget, array $data): Budget
    {
        return DB::transaction(function () use ($budget, $data): Budget {
            $depenses = Depense::query()->where('id_utilisateur', $budget->id_utilisateur)->whereNull('id_budget_historique')->get();
            $revenus = Revenu::query()->where('id_utilisateur', $budget->id_utilisateur)->whereNull('id_budget_historique')->get();
            $historique = BudgetHistorique::create([
                'id_budget' => $budget->id_budget, 'id_utilisateur' => $budget->id_utilisateur,
                'periode' => $budget->periode, 'montant_total' => $budget->montant_total,
                'solde_final' => $budget->solde, 'montant_depense' => $depenses->sum('montant'),
                'date_debut' => $budget->date_debut, 'date_fin' => $budget->date_fin,
            ]);
            Depense::query()->whereKey($depenses->pluck('id_depense'))->update(['id_budget_historique' => $historique->id_budget_historique]);
            Revenu::query()->whereKey($revenus->pluck('id_revenu'))->update(['id_budget_historique' => $historique->id_budget_historique]);
            $budget->update([...$data, 'solde' => $data['montant_total']]);

            return $budget->refresh();
        });
    }
}
