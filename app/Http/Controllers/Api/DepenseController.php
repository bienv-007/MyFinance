<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepenseRequest;
use App\Http\Resources\DepenseResource;
use App\Models\Budget;
use App\Models\Depense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DepenseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Depense::query()
            ->with('categorie')
            ->where('id_utilisateur', $request->user()->id_utilisateur);

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
        $this->ensureBudgetLimit($request, $request->validated());

        $depense = Depense::create([
            ...$request->validated(),
            'id_utilisateur' => $request->user()->id_utilisateur,
        ]);

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
        $this->ensureBudgetLimit($request, $request->validated(), $depense);
        $depense->update($request->validated());

        return response()->json(['data' => new DepenseResource($depense->load('categorie'))]);
    }

    public function destroy(Request $request, Depense $depense): JsonResponse
    {
        abort_unless($depense->id_utilisateur === $request->user()->id_utilisateur, 403);
        $depense->delete();

        return response()->json(['message' => 'Dépense supprimée.']);
    }

    private function ensureBudgetLimit(Request $request, array $data, ?Depense $current = null): void
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
            $spent = Depense::query()
                ->where('id_utilisateur', $request->user()->id_utilisateur)
                ->whereBetween('date_depense', [$budget->date_debut, $budget->date_fin])
                ->when($current, fn ($query) => $query->where($current->getKeyName(), '!=', $current->getKey()))
                ->sum('montant');

            if ((float) $spent + (float) $data['montant'] > (float) $budget->montant_total) {
                throw ValidationException::withMessages([
                    'montant' => sprintf(
                        'Cette dépense dépasse le budget disponible (%s FC restants).',
                        number_format(max(0, (float) $budget->montant_total - (float) $spent), 2, ',', ' '),
                    ),
                ]);
            }
        }
    }
}
