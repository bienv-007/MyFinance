<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DepenseRequest;
use App\Http\Resources\DepenseResource;
use App\Models\Depense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $depense->update($request->validated());

        return response()->json(['data' => new DepenseResource($depense->load('categorie'))]);
    }

    public function destroy(Request $request, Depense $depense): JsonResponse
    {
        abort_unless($depense->id_utilisateur === $request->user()->id_utilisateur, 403);
        $depense->delete();

        return response()->json(['message' => 'Dépense supprimée.']);
    }
}
