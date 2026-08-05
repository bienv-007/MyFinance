<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RevenuPrevisionRequest;
use App\Http\Resources\RevenuPrevisionResource;
use App\Models\RevenuPrevision;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RevenuPrevisionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = RevenuPrevision::query()->where('id_utilisateur', $request->user()->id_utilisateur);

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('source_previsionnelle', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $query->orderBy(in_array($sort, ['date_previsionnelle', 'montant_previsionnel'], true) ? $sort : 'id_revenu_prevision', $direction);

        return RevenuPrevisionResource::collection($query->paginate(10));
    }

    public function store(RevenuPrevisionRequest $request): JsonResponse
    {
        $revenuPrevision = RevenuPrevision::create([
            ...$request->validated(),
            'id_utilisateur' => $request->user()->id_utilisateur,
        ]);

        return response()->json(['data' => new RevenuPrevisionResource($revenuPrevision)], 201);
    }

    public function show(Request $request, RevenuPrevision $revenu_prevision): JsonResponse
    {
        abort_unless($revenu_prevision->id_utilisateur === $request->user()->id_utilisateur, 403);

        return response()->json(['data' => new RevenuPrevisionResource($revenu_prevision)]);
    }

    public function update(RevenuPrevisionRequest $request, RevenuPrevision $revenu_prevision): JsonResponse
    {
        abort_unless($revenu_prevision->id_utilisateur === $request->user()->id_utilisateur, 403);
        $revenu_prevision->update($request->validated());

        return response()->json(['data' => new RevenuPrevisionResource($revenu_prevision)]);
    }

    public function destroy(Request $request, RevenuPrevision $revenu_prevision): JsonResponse
    {
        abort_unless($revenu_prevision->id_utilisateur === $request->user()->id_utilisateur, 403);
        $revenu_prevision->delete();

        return response()->json(['message' => 'Prévision de revenu supprimée.']);
    }
}
