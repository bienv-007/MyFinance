<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\BudgetHistorique;
use App\Models\Depense;
use App\Services\BudgetCycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class BudgetController extends Controller
{
    public function __construct(private readonly BudgetCycleService $cycles) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->cycles->archiveCompletedAndExpiredBudgets();

        $query = Budget::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->where('est_archive', false);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('periode', 'like', "%{$search}%")
                    ->orWhere('montant_total', 'like', "%{$search}%");
            });
        }

        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $query->orderBy(in_array($sort, ['periode', 'date_debut', 'date_fin', 'montant_total'], true) ? $sort : 'id_budget', $direction);

        $today = today()->toDateString();
        $stats = Budget::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->where('est_archive', false)
            ->selectRaw('COUNT(*) as total_budgets')
            ->selectRaw('COALESCE(SUM(montant_total), 0) as montant_total')
            ->selectRaw(
                'SUM(CASE WHEN date_debut <= ? AND date_fin >= ? THEN 1 ELSE 0 END) as budgets_actifs',
                [$today, $today],
            )
            ->first();
        $budgets = Budget::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->where('est_archive', false)
            ->get();
        $montantDepense = (float) Depense::query()->where('id_utilisateur', $request->user()->id_utilisateur)->whereNull('id_budget_historique')->sum('montant');
        $montantInitial = (float) ($stats?->montant_total ?? 0);
        $montantRestant = (float) $budgets->sum('solde');
        $historiques = BudgetHistorique::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->latest('date_archivage')
            ->get()
            ->map(fn (BudgetHistorique $historique): array => [
                'id_budget_historique' => $historique->id_budget_historique,
                'id_budget' => $historique->id_budget,
                'periode' => $historique->periode,
                'montant_total' => $historique->montant_total,
                'solde' => $historique->solde_final,
                'date_debut' => $historique->date_debut?->format('Y-m-d'),
                'date_fin' => $historique->date_fin?->format('Y-m-d'),
                'statut' => 'Archivé',
                'est_historique' => true,
            ]);

        return BudgetResource::collection($query->paginate(10))->additional([
            'stats' => [
                'total' => (int) ($stats?->total_budgets ?? 0),
                'actifs' => (int) ($stats?->budgets_actifs ?? 0),
                'montant_total' => (float) ($stats?->montant_total ?? 0),
                'montant_initial' => $montantInitial,
                'montant_depense' => $montantDepense,
                'montant_restant' => $montantRestant,
            ],
            'historiques' => $historiques,
        ]);
    }

    public function history(Request $request): JsonResponse
    {
        $historiques = BudgetHistorique::query()
            ->with(['depenses.categorie', 'revenus'])
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->latest('date_archivage')
            ->get()
            ->map(fn (BudgetHistorique $historique): array => [
                'id_budget_historique' => $historique->id_budget_historique,
                'periode' => $historique->periode,
                'montant_total' => $historique->montant_total,
                'montant_depense' => $historique->montant_depense,
                'solde_final' => $historique->solde_final,
                'date_archivage' => $historique->date_archivage?->toISOString(),
                'depenses' => $historique->depenses,
                'revenus' => $historique->revenus,
            ]);

        return response()->json(['data' => $historiques]);
    }

    public function store(BudgetRequest $request): JsonResponse
    {
        $this->ensureUserHasNoBudget($request);
        $data = $request->validated();
        unset($data['reinitialiser_solde']);

        $budget = Budget::create([
            ...$data,
            'id_utilisateur' => $request->user()->id_utilisateur,
            'solde' => $data['montant_total'],
        ]);

        return response()->json(['data' => new BudgetResource($budget)], 201);
    }

    public function show(Request $request, Budget $budget): JsonResponse
    {
        abort_unless($budget->id_utilisateur === $request->user()->id_utilisateur, 403);

        return response()->json(['data' => new BudgetResource($budget)]);
    }

    public function update(BudgetRequest $request, Budget $budget): JsonResponse
    {
        abort_unless($budget->id_utilisateur === $request->user()->id_utilisateur, 403);
        $data = $request->validated();
        unset($data['reinitialiser_solde']);
        $this->cycles->restart($budget, $data);

        return response()->json(['data' => new BudgetResource($budget)]);
    }

    public function destroy(Request $request, Budget $budget): JsonResponse
    {
        abort_unless($budget->id_utilisateur === $request->user()->id_utilisateur, 403);
        $budget->delete();

        return response()->json(['message' => 'Budget supprimé.']);
    }

    private function ensureUserHasNoBudget(BudgetRequest $request): void
    {
        $hasBudget = Budget::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->where('est_archive', false)
            ->exists();

        if ($hasBudget) {
            throw ValidationException::withMessages([
                'budget' => 'Vous ne pouvez avoir qu’un seul budget. Modifiez le budget existant.',
            ]);
        }
    }
}
