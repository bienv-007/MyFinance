<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RevenuRequest;
use App\Http\Resources\RevenuResource;
use App\Models\Revenu;
use App\Models\Budget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RevenuController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Revenu::query()->where('id_utilisateur', $request->user()->id_utilisateur);

        if ($request->boolean('cycle_actif')) {
            $query->whereNull('id_budget_historique');
        }

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('source', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort === 'date_revenu' ? 'date_revenu' : 'id_revenu', $direction);

        return response()->json(RevenuResource::collection($query->paginate(10)));
    }

    public function store(RevenuRequest $request): JsonResponse
    {
        $revenu = Revenu::create([
            ...$request->validated(),
            'id_utilisateur' => $request->user()->id_utilisateur,
            'id_budget' => Budget::query()->where('id_utilisateur', $request->user()->id_utilisateur)->whereDate('date_debut', '<=', $request->date_revenu)->whereDate('date_fin', '>=', $request->date_revenu)->value('id_budget'),
        ]);

        return response()->json(['data' => new RevenuResource($revenu)], 201);
    }

    public function show(Request $request, Revenu $revenu): JsonResponse
    {
        abort_unless($revenu->id_utilisateur === $request->user()->id_utilisateur, 403);

        return response()->json(['data' => new RevenuResource($revenu)]);
    }

    public function update(RevenuRequest $request, Revenu $revenu): JsonResponse
    {
        abort_unless($revenu->id_utilisateur === $request->user()->id_utilisateur, 403);
        $revenu->update($request->validated());

        return response()->json(['data' => new RevenuResource($revenu)]);
    }

    public function destroy(Request $request, Revenu $revenu): JsonResponse
    {
        abort_unless($revenu->id_utilisateur === $request->user()->id_utilisateur, 403);
        $revenu->delete();

        return response()->json(['message' => 'Revenu supprimé.']);
    }
}
