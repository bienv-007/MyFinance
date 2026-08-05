<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepensePrevisionRequest;
use App\Http\Resources\DepensePrevisionResource;
use App\Models\DepensePrevision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DepensePrevisionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = DepensePrevision::query()->with('categorie')->where('id_utilisateur', $request->user()->id_utilisateur);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('categorie', fn ($category) => $category->where('nom_categorie', 'like', "%{$search}%"));
            });
        }

        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $query->orderBy(in_array($sort, ['date_previsionnelle', 'montant_previsionnel'], true) ? $sort : 'id_depense_prevision', $direction);

        return DepensePrevisionResource::collection($query->paginate(10));
    }

    public function store(DepensePrevisionRequest $request): JsonResponse
    {
        $depensePrevision = DepensePrevision::create([
            ...$request->validated(),
            'id_utilisateur' => $request->user()->id_utilisateur,
        ]);

        return response()->json(['data' => new DepensePrevisionResource($depensePrevision->load('categorie'))], 201);
    }

    public function show(Request $request, DepensePrevision $depense_prevision): JsonResponse
    {
        abort_unless($depense_prevision->id_utilisateur === $request->user()->id_utilisateur, 403);

        return response()->json(['data' => new DepensePrevisionResource($depense_prevision->load('categorie'))]);
    }

    public function update(DepensePrevisionRequest $request, DepensePrevision $depense_prevision): JsonResponse
    {
        abort_unless($depense_prevision->id_utilisateur === $request->user()->id_utilisateur, 403);
        $depense_prevision->update($request->validated());

        return response()->json(['data' => new DepensePrevisionResource($depense_prevision->load('categorie'))]);
    }

    public function destroy(Request $request, DepensePrevision $depense_prevision): JsonResponse
    {
        abort_unless($depense_prevision->id_utilisateur === $request->user()->id_utilisateur, 403);
        $depense_prevision->delete();

        return response()->json(['message' => 'Prévision de dépense supprimée.']);
    }
}
