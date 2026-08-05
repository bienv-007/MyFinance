<?php

namespace App\Http\Controllers;

use App\Actions\MarkRevenuPrevisionAsReceived;
use App\Http\Requests\RevenuPrevisionRequest;
use App\Models\RevenuPrevision;
use App\Services\RevenuPrevisionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevenuPrevisionController extends Controller
{
    public function __construct(private readonly RevenuPrevisionService $service) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $query = $this->service->queryFor($user)->withRealizedStatus();
        $filters = $this->service->applyFilters($query, $request);

        $previsions = $query
            ->orderBy($filters['sort'], $filters['direction'])
            ->orderByDesc('id_revenu_prevision')
            ->paginate(10)
            ->withQueryString();

        return view('revenu-previsions.index', [
            'previsions' => $previsions,
            'filters' => $filters,
            'stats' => $this->service->statistics($user),
        ]);
    }

    public function create(): View
    {
        return view('revenu-previsions.create', ['prevision' => new RevenuPrevision]);
    }

    public function store(RevenuPrevisionRequest $request): RedirectResponse
    {
        $request->user()->revenuPrevisions()->create($request->validated());

        return redirect()
            ->route('revenu-previsions.index')
            ->with('success', 'La prévision de revenu a été créée avec succès.');
    }

    public function show(Request $request, RevenuPrevision $revenu_prevision): View
    {
        $this->ensureOwnership($request, $revenu_prevision);

        return view('revenu-previsions.show', ['prevision' => $revenu_prevision]);
    }

    public function edit(Request $request, RevenuPrevision $revenu_prevision): View
    {
        $this->ensureOwnership($request, $revenu_prevision);

        return view('revenu-previsions.edit', ['prevision' => $revenu_prevision]);
    }

    public function update(RevenuPrevisionRequest $request, RevenuPrevision $revenu_prevision): RedirectResponse
    {
        $this->ensureOwnership($request, $revenu_prevision);
        $revenu_prevision->update($request->validated());

        return redirect()
            ->route('revenu-previsions.index')
            ->with('success', 'La prévision de revenu a été modifiée avec succès.');
    }

    public function destroy(Request $request, RevenuPrevision $revenu_prevision): RedirectResponse
    {
        $this->ensureOwnership($request, $revenu_prevision);
        $revenu_prevision->delete();

        return redirect()
            ->route('revenu-previsions.index')
            ->with('success', 'La prévision de revenu a été supprimée avec succès.');
    }

    public function markAsReceived(
        Request $request,
        RevenuPrevision $revenu_prevision,
        MarkRevenuPrevisionAsReceived $action,
    ): RedirectResponse {
        $this->ensureOwnership($request, $revenu_prevision);
        $action->execute($revenu_prevision);

        return redirect()
            ->route('revenu-previsions.index')
            ->with('success', 'Le revenu a été marqué comme perçu et enregistré.');
    }

    private function ensureOwnership(Request $request, RevenuPrevision $prevision): void
    {
        abort_unless($prevision->id_utilisateur === $request->user()->id_utilisateur, 404);
    }
}
