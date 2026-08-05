<?php

namespace App\Http\Controllers\Api;

use App\Actions\MarkRevenuPrevisionAsReceived;
use App\Http\Controllers\Controller;
use App\Http\Requests\RevenuPrevisionRequest;
use App\Http\Resources\RevenuPrevisionResource;
use App\Http\Resources\RevenuResource;
use App\Models\RevenuPrevision;
use App\Models\User;
use App\Services\RevenuPrevisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RevenuPrevisionController extends Controller
{
    public function __construct(private readonly RevenuPrevisionService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = $this->service->queryFor($request->user())->withRealizedStatus();
        $filters = $this->service->applyFilters($query, $request);

        return RevenuPrevisionResource::collection(
            $query
                ->orderBy($filters['sort'], $filters['direction'])
                ->orderByDesc('id_revenu_prevision')
                ->paginate(10),
        )->additional(['stats' => $this->statisticsPayload($request->user())]);
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

    public function markAsReceived(
        Request $request,
        RevenuPrevision $revenu_prevision,
        MarkRevenuPrevisionAsReceived $action,
    ): JsonResponse {
        abort_unless($revenu_prevision->id_utilisateur === $request->user()->id_utilisateur, 403);
        $revenu = $action->execute($revenu_prevision);

        return response()->json([
            'message' => 'Le revenu a été marqué comme perçu et enregistré.',
            'data' => new RevenuResource($revenu),
        ], 201);
    }

    private function statisticsPayload(User $user): array
    {
        $stats = $this->service->statistics($user);

        return [
            'total' => $stats['total'],
            'montant_total' => $stats['montant_total'],
            'montant_mois' => $stats['montant_mois'],
            'montant_annee' => $stats['montant_annee'],
            'attendus' => $stats['attendus'],
            'expirees' => $stats['expirees'],
            'prochaine_date' => $stats['prochaine']?->date_previsionnelle?->format('Y-m-d'),
            'prochaine_source' => $stats['prochaine']?->source_previsionnelle,
            'source_principale' => $stats['source_principale'],
            'plus_elevee_montant' => $stats['plus_elevee']?->montant_previsionnel,
            'plus_elevee_source' => $stats['plus_elevee']?->source_previsionnelle,
            'plus_elevee_date' => $stats['plus_elevee']?->date_previsionnelle?->format('Y-m-d'),
        ];
    }
}
