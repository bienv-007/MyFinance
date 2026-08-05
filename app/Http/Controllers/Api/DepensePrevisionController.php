<?php

namespace App\Http\Controllers\Api;

use App\Actions\ConvertDepensePrevisionToDepense;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepensePrevisionRequest;
use App\Http\Resources\DepensePrevisionResource;
use App\Http\Resources\DepenseResource;
use App\Models\DepensePrevision;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DepensePrevisionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $userId = $request->user()->id_utilisateur;
        $query = DepensePrevision::query()->with('categorie')->where('id_utilisateur', $userId);
        $categoryId = $request->integer('id_categorie') ?: null;
        $date = $request->string('date')->toString();
        $month = $request->string('mois')->toString();
        $minimumAmount = $request->input('montant_min');
        $maximumAmount = $request->input('montant_max');

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search): void {
                $q->where('description', 'like', "%{$search}%")
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

        $sort = $request->string('sort')->toString();
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';
        $query->orderBy(in_array($sort, ['date_previsionnelle', 'montant_previsionnel'], true) ? $sort : 'id_depense_prevision', $direction);

        $today = today();
        $aggregate = DepensePrevision::query()
            ->where('id_utilisateur', $userId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COALESCE(SUM(montant_previsionnel), 0) as montant_total')
            ->selectRaw('SUM(CASE WHEN date_previsionnelle >= ? THEN 1 ELSE 0 END) as en_attente', [$today->toDateString()])
            ->selectRaw('SUM(CASE WHEN date_previsionnelle < ? THEN 1 ELSE 0 END) as depassees', [$today->toDateString()])
            ->first();
        $next = DepensePrevision::query()
            ->with('categorie')
            ->where('id_utilisateur', $userId)
            ->whereDate('date_previsionnelle', '>=', $today)
            ->orderBy('date_previsionnelle')
            ->first();
        $mostUsedCategory = DepensePrevision::query()
            ->with('categorie')
            ->where('id_utilisateur', $userId)
            ->select('id_categorie')
            ->selectRaw('COUNT(*) as occurrences')
            ->groupBy('id_categorie')
            ->orderByDesc('occurrences')
            ->first();

        return DepensePrevisionResource::collection($query->paginate(10))->additional([
            'stats' => [
                'total' => (int) ($aggregate?->total ?? 0),
                'montant_total' => (float) ($aggregate?->montant_total ?? 0),
                'en_attente' => (int) ($aggregate?->en_attente ?? 0),
                'depassees' => (int) ($aggregate?->depassees ?? 0),
                'prochaine_date' => $next?->date_previsionnelle?->format('Y-m-d'),
                'prochaine_categorie' => $next?->categorie?->nom_categorie,
                'categorie_frequente' => $mostUsedCategory?->categorie?->nom_categorie,
            ],
        ]);
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

    public function validate(
        Request $request,
        DepensePrevision $depense_prevision,
        ConvertDepensePrevisionToDepense $converter,
    ): JsonResponse {
        abort_unless($depense_prevision->id_utilisateur === $request->user()->id_utilisateur, 403);
        $depense = $converter->execute($depense_prevision);

        return response()->json([
            'message' => 'Prévision validée et dépense enregistrée.',
            'data' => new DepenseResource($depense),
        ], 201);
    }
}
