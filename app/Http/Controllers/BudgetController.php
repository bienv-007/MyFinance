<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Models\Budget;
use App\Services\BudgetCycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct(private readonly BudgetCycleService $cycles) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim($request->string('search')->toString());
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['periode', 'date_debut', 'date_fin', 'montant_total'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'date_debut';

        $budgetQuery = $user->budgets();

        if ($search !== '') {
            $budgetQuery->where(function ($query) use ($search): void {
                $query->where('periode', 'like', "%{$search}%")
                    ->orWhere('montant_total', 'like', "%{$search}%")
                    ->orWhere('date_debut', 'like', "%{$search}%")
                    ->orWhere('date_fin', 'like', "%{$search}%");
            });
        }

        $budgets = $budgetQuery
            ->orderBy($sort, $direction)
            ->orderByDesc('id_budget')
            ->paginate(9)
            ->withQueryString();

        $today = today()->toDateString();
        $stats = $user->budgets()
            ->selectRaw('COUNT(*) as total_budgets')
            ->selectRaw('COALESCE(SUM(montant_total), 0) as montant_total')
            ->selectRaw(
                'SUM(CASE WHEN date_debut <= ? AND date_fin >= ? THEN 1 ELSE 0 END) as budgets_actifs',
                [$today, $today],
            )
            ->first();

        return view('budgets.index', [
            'budgets' => $budgets,
            'search' => $search,
            'sort' => $sort,
            'direction' => $direction,
            'stats' => [
                'total' => (int) ($stats?->total_budgets ?? 0),
                'actifs' => (int) ($stats?->budgets_actifs ?? 0),
                'montant_total' => (float) ($stats?->montant_total ?? 0),
            ],
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $budget = $request->user()->budgets()->first();

        if ($budget !== null) {
            return redirect()
                ->route('budgets.edit', $budget)
                ->with('error', 'Vous ne pouvez avoir qu’un seul budget. Modifiez le budget existant.');
        }

        return view('budgets.create', ['budget' => new Budget]);
    }

    public function store(BudgetRequest $request): RedirectResponse
    {
        $this->ensureUserHasNoBudget($request);
        $data = $request->validated();
        unset($data['reinitialiser_solde']);
        $data['solde'] = $data['montant_total'];
        $request->user()->budgets()->create($data);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Le budget a été créé avec succès.');
    }

    public function show(Request $request, Budget $budget): View
    {
        $this->ensureOwnership($request, $budget);

        return view('budgets.show', compact('budget'));
    }

    public function edit(Request $request, Budget $budget): View
    {
        $this->ensureOwnership($request, $budget);

        return view('budgets.edit', compact('budget'));
    }

    public function update(BudgetRequest $request, Budget $budget): RedirectResponse
    {
        $this->ensureOwnership($request, $budget);
        $data = $request->validated();
        unset($data['reinitialiser_solde']);
        $this->cycles->restart($budget, $data);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Le budget a été modifié, archivé et ses dépenses ont été réinitialisées.');
    }

    public function history(Request $request, Budget $budget): View
    {
        $this->ensureOwnership($request, $budget);
        $historiques = $budget->historiques()->with(['depenses', 'revenus'])->latest('date_archivage')->paginate(15);

        return view('budgets.history', compact('budget', 'historiques'));
    }

    public function destroy(Request $request, Budget $budget): RedirectResponse
    {
        $this->ensureOwnership($request, $budget);
        $budget->delete();

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Le budget a été supprimé avec succès.');
    }

    private function ensureOwnership(Request $request, Budget $budget): void
    {
        abort_unless($budget->id_utilisateur === $request->user()->id_utilisateur, 404);
    }

    private function ensureUserHasNoBudget(Request $request): void
    {
        if ($request->user()->budgets()->exists()) {
            throw ValidationException::withMessages([
                'budget' => 'Vous ne pouvez avoir qu’un seul budget. Modifiez le budget existant.',
            ]);
        }
    }
}
