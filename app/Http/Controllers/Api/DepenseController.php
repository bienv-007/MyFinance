<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepenseRequest;
use App\Http\Resources\DepenseResource;
use App\Models\Budget;
use App\Models\Depense;
use App\Services\BudgetCycleService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DepenseController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly BudgetCycleService $cycles,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Depense::query()
            ->with('categorie')
            ->where('id_utilisateur', $request->user()->id_utilisateur);

        if ($request->boolean('cycle_actif')) {
            $query->whereNull('id_budget_historique');
        }

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('categorie', fn ($category) => $category->where('nom_categorie', 'like', "%{$search}%"));
            });
        }

        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort === 'date_depense' ? 'date_depense' : 'id_depense', $direction);

        return response()->json(DepenseResource::collection($query->paginate(10)));
    }

    public function store(DepenseRequest $request): JsonResponse
    {
        $data = $request->validated();
        $depense = DB::transaction(function () use ($request, $data): Depense {
            $this->ensureBudgetLimit($request, $data);

            $depense = Depense::create([
                ...$data,
                'id_utilisateur' => $request->user()->id_utilisateur,
                'id_budget' => $this->budgetsForDate($request, $data['date_depense'])->first()?->id_budget,
            ]);
            $this->debitBudget($request, $data);

            return $depense;
        });

        return response()->json(['data' => new DepenseResource($depense->load('categorie'))], 201);
    }

    public function show(Request $request, Depense $depense): JsonResponse
    {
        abort_unless($depense->id_utilisateur === $request->user()->id_utilisateur, 403);

        return response()->json(['data' => new DepenseResource($depense->load('categorie'))]);
    }

    public function update(DepenseRequest $request, Depense $depense): JsonResponse
    {
        abort_unless($depense->id_utilisateur === $request->user()->id_utilisateur, 403);
        $data = $request->validated();

        DB::transaction(function () use ($request, $depense, $data): void {
            if ($depense->id_budget_historique !== null) {
                $depense->update($data);

                return;
            }
            $this->creditBudget($request, [
                'date_depense' => $depense->date_depense->toDateString(),
                'montant' => $depense->montant,
            ]);
            $this->ensureBudgetLimit($request, $data);
            $depense->update($data);
            $this->debitBudget($request, $data);
        });

        return response()->json(['data' => new DepenseResource($depense->load('categorie'))]);
    }

    public function destroy(Request $request, Depense $depense): JsonResponse
    {
        abort_unless($depense->id_utilisateur === $request->user()->id_utilisateur, 403);
        DB::transaction(function () use ($request, $depense): void {
            if ($depense->id_budget_historique !== null) {
                $depense->delete();

                return;
            }
            $this->creditBudget($request, [
                'date_depense' => $depense->date_depense->toDateString(),
                'montant' => $depense->montant,
            ]);
            $depense->delete();
        });

        return response()->json(['message' => 'Dépense supprimée.']);
    }

    private function ensureBudgetLimit(Request $request, array $data): void
    {
        $budgets = Budget::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->whereDate('date_debut', '<=', $data['date_depense'])
            ->whereDate('date_fin', '>=', $data['date_depense'])
            ->get();

        if ($budgets->isEmpty()) {
            throw ValidationException::withMessages([
                'date_depense' => 'Aucun budget disponible pour la date de cette dépense.',
            ]);
        }

        foreach ($budgets as $budget) {
            $solde = (float) $budget->solde;

            if ((float) $data['montant'] > $solde) {
                throw ValidationException::withMessages([
                    'montant' => sprintf(
                        'Cette dépense dépasse le budget disponible (%s FC restants).',
                        number_format($solde, 2, ',', ' '),
                    ),
                ]);
            }
        }
    }

    private function debitBudget(Request $request, array $data): void
    {
        $this->budgetsForDate($request, $data['date_depense'])
            ->each(function (Budget $budget) use ($data): void {
                $budget->decrement('solde', $data['montant']);
                $this->notifications->notifyBudgetUsageThresholds($budget->refresh());
                $this->cycles->archiveIfNecessary($budget);
            });
    }

    private function creditBudget(Request $request, array $data): void
    {
        $this->budgetsForDate($request, $data['date_depense'])
            ->each(fn (Budget $budget) => $budget->increment('solde', $data['montant']));
    }

    private function budgetsForDate(Request $request, string $date): Collection
    {
        return Budget::query()
            ->where('id_utilisateur', $request->user()->id_utilisateur)
            ->where('est_archive', false)
            ->whereDate('date_debut', '<=', $date)
            ->whereDate('date_fin', '>=', $date)
            ->get();
    }
}
