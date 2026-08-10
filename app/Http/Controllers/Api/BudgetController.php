<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use App\Models\Depense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class BudgetController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Budget::query()->where('id_utilisateur', $request->user()->id_utilisateur);

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
            ->selectRaw('COUNT(*) as total_budgets')
            ->selectRaw('COALESCE(SUM(montant_total), 0) as montant_total')
            ->selectRaw(
                'SUM(CASE WHEN date_debut <= ? AND date_fin >= ? THEN 1 ELSE 0 END) as budgets_actifs',
                [$today, $today],
            )
            ->first();
        $budgets = Budget::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->get();
        $montantDepense = $budgets->sum(fn (Budget $budget): float => (float) Depense::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->whereBetween('date_depense', [$budget->date_debut, $budget->date_fin])
            ->sum('montant'));
        $montantInitial = (float) ($stats?->montant_total ?? 0);

        return BudgetResource::collection($query->paginate(10))->additional([
            'stats' => [
                'total' => (int) ($stats?->total_budgets ?? 0),
                'actifs' => (int) ($stats?->budgets_actifs ?? 0),
                'montant_total' => (float) ($stats?->montant_total ?? 0),
                'montant_initial' => $montantInitial,
                'montant_depense' => $montantDepense,
                'montant_restant' => max(0, $montantInitial - $montantDepense),
            ],
        ]);
    }

    public function store(BudgetRequest $request): JsonResponse
    {
        $this->ensureNoOverlappingBudget($request);

        $budget = Budget::create([
            ...$request->validated(),
            'id_utilisateur' => $request->user()->id_utilisateur,
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
        $this->ensureNoOverlappingBudget($request, $budget);
        $budget->update($request->validated());

        return response()->json(['data' => new BudgetResource($budget)]);
    }

    public function destroy(Request $request, Budget $budget): JsonResponse
    {
        abort_unless($budget->id_utilisateur === $request->user()->id_utilisateur, 403);
        $budget->delete();

        return response()->json(['message' => 'Budget supprimé.']);
    }

    private function ensureNoOverlappingBudget(BudgetRequest $request, ?Budget $current = null): void
    {
        $data = $request->validated();
        $overlap = Budget::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->whereDate('date_debut', '<=', $data['date_fin'])
            ->whereDate('date_fin', '>=', $data['date_debut'])
            ->when($current, fn ($query) => $query->where('id_budget', '!=', $current->id_budget))
            ->exists();

        if ($overlap) {
            throw ValidationException::withMessages([
                'date_debut' => 'Cette période chevauche déjà un autre budget.',
            ]);
        }
    }
}
