<?php

namespace App\Http\Controllers;

use App\Actions\ConvertDepensePrevisionToDepense;
use App\Http\Requests\DepensePrevisionRequest;
use App\Models\Categorie;
use App\Models\DepensePrevision;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepensePrevisionController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $search = trim($request->string('search')->toString());
        $categoryId = $request->integer('id_categorie') ?: null;
        $date = $request->string('date')->toString();
        $month = $request->string('mois')->toString();
        $minimumAmount = $request->input('montant_min');
        $maximumAmount = $request->input('montant_max');
        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['date_previsionnelle', 'montant_previsionnel', 'id_categorie'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'date_previsionnelle';

        $query = $user->depensePrevisions()->with('categorie');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('description', 'like', "%{$search}%")
                    ->orWhere('montant_previsionnel', 'like', "%{$search}%")
                    ->orWhereHas('categorie', fn ($category) => $category->where('nom_categorie', 'like', "%{$search}%"));
            });
        }

        $query->when($categoryId, fn ($builder) => $builder->where('id_categorie', $categoryId));
        $query->when($date !== '', fn ($builder) => $builder->whereDate('date_previsionnelle', $date));

        $monthNumber = (int) substr($month, 5, 2);

        if (preg_match('/^\d{4}-\d{2}$/', $month) === 1 && $monthNumber >= 1 && $monthNumber <= 12) {
            $monthDate = Carbon::createFromFormat('Y-m', $month);
            $query->whereBetween('date_previsionnelle', [
                $monthDate->copy()->startOfMonth()->toDateString(),
                $monthDate->copy()->endOfMonth()->toDateString(),
            ]);
        }

        $query->when(is_numeric($minimumAmount), fn ($builder) => $builder->where('montant_previsionnel', '>=', $minimumAmount));
        $query->when(is_numeric($maximumAmount), fn ($builder) => $builder->where('montant_previsionnel', '<=', $maximumAmount));

        $previsions = $query
            ->orderBy($sort, $direction)
            ->orderByDesc('id_depense_prevision')
            ->paginate(10)
            ->withQueryString();

        return view('depense-previsions.index', [
            'previsions' => $previsions,
            'categories' => Categorie::query()->orderBy('nom_categorie')->get(),
            'filters' => [
                'search' => $search,
                'id_categorie' => $categoryId,
                'date' => $date,
                'mois' => $month,
                'montant_min' => $minimumAmount,
                'montant_max' => $maximumAmount,
                'sort' => $sort,
                'direction' => $direction,
            ],
            'stats' => $this->statistics($user),
        ]);
    }

    public function create(): View
    {
        return view('depense-previsions.create', [
            'prevision' => new DepensePrevision,
            'categories' => Categorie::query()->orderBy('nom_categorie')->get(),
        ]);
    }

    public function store(DepensePrevisionRequest $request): RedirectResponse
    {
        $request->user()->depensePrevisions()->create($request->validated());

        return redirect()
            ->route('depense-previsions.index')
            ->with('success', 'La prévision de dépense a été créée avec succès.');
    }

    public function show(Request $request, DepensePrevision $depense_prevision): View
    {
        $this->ensureOwnership($request, $depense_prevision);
        $depense_prevision->load('categorie');

        return view('depense-previsions.show', ['prevision' => $depense_prevision]);
    }

    public function edit(Request $request, DepensePrevision $depense_prevision): View
    {
        $this->ensureOwnership($request, $depense_prevision);

        return view('depense-previsions.edit', [
            'prevision' => $depense_prevision,
            'categories' => Categorie::query()->orderBy('nom_categorie')->get(),
        ]);
    }

    public function update(DepensePrevisionRequest $request, DepensePrevision $depense_prevision): RedirectResponse
    {
        $this->ensureOwnership($request, $depense_prevision);
        $depense_prevision->update($request->validated());

        return redirect()
            ->route('depense-previsions.index')
            ->with('success', 'La prévision de dépense a été modifiée avec succès.');
    }

    public function destroy(Request $request, DepensePrevision $depense_prevision): RedirectResponse
    {
        $this->ensureOwnership($request, $depense_prevision);
        $depense_prevision->delete();

        return redirect()
            ->route('depense-previsions.index')
            ->with('success', 'La prévision de dépense a été supprimée avec succès.');
    }

    public function validate(
        Request $request,
        DepensePrevision $depense_prevision,
        ConvertDepensePrevisionToDepense $converter,
    ): RedirectResponse {
        $this->ensureOwnership($request, $depense_prevision);
        $converter->execute($depense_prevision);

        return redirect()
            ->route('depense-previsions.index')
            ->with('success', 'La prévision a été validée et enregistrée dans les dépenses.');
    }

    private function statistics(User $user): array
    {
        $today = today();
        $monthStart = $today->copy()->startOfMonth()->toDateString();
        $monthEnd = $today->copy()->endOfMonth()->toDateString();
        $yearStart = $today->copy()->startOfYear()->toDateString();
        $yearEnd = $today->copy()->endOfYear()->toDateString();

        $aggregate = $user->depensePrevisions()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COALESCE(SUM(montant_previsionnel), 0) as montant_total')
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN date_previsionnelle BETWEEN ? AND ? THEN montant_previsionnel ELSE 0 END), 0) as montant_mois',
                [$monthStart, $monthEnd],
            )
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN date_previsionnelle BETWEEN ? AND ? THEN montant_previsionnel ELSE 0 END), 0) as montant_annee',
                [$yearStart, $yearEnd],
            )
            ->selectRaw('SUM(CASE WHEN date_previsionnelle >= ? THEN 1 ELSE 0 END) as en_attente', [$today->toDateString()])
            ->selectRaw('SUM(CASE WHEN date_previsionnelle < ? THEN 1 ELSE 0 END) as depassees', [$today->toDateString()])
            ->first();

        $next = $user->depensePrevisions()
            ->with('categorie')
            ->whereDate('date_previsionnelle', '>=', $today)
            ->orderBy('date_previsionnelle')
            ->orderBy('id_depense_prevision')
            ->first();

        $mostUsedCategory = $user->depensePrevisions()
            ->select('id_categorie')
            ->selectRaw('COUNT(*) as occurrences')
            ->with('categorie')
            ->groupBy('id_categorie')
            ->orderByDesc('occurrences')
            ->first();

        return [
            'total' => (int) ($aggregate?->total ?? 0),
            'montant_total' => (float) ($aggregate?->montant_total ?? 0),
            'montant_mois' => (float) ($aggregate?->montant_mois ?? 0),
            'montant_annee' => (float) ($aggregate?->montant_annee ?? 0),
            'en_attente' => (int) ($aggregate?->en_attente ?? 0),
            'depassees' => (int) ($aggregate?->depassees ?? 0),
            'prochaine' => $next,
            'categorie_frequente' => $mostUsedCategory?->categorie,
        ];
    }

    private function ensureOwnership(Request $request, DepensePrevision $prevision): void
    {
        abort_unless($prevision->id_utilisateur === $request->user()->id_utilisateur, 404);
    }
}
