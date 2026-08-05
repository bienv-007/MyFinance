@props(['budget'])

<article class="group rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/40 transition hover:-translate-y-0.5 hover:shadow-lg hover:shadow-slate-200/60">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Période</p>
            <h3 class="mt-1 text-lg font-bold text-slate-950">{{ $budget->periode }}</h3>
        </div>
        <x-budget-status-badge :statut="$budget->statut" />
    </div>

    <div class="mt-6 rounded-2xl bg-slate-50 px-4 py-3">
        <p class="text-xs font-medium text-slate-500">Montant total</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-slate-950">{{ number_format((float) $budget->montant_total, 2, ',', ' ') }} <span class="text-sm font-semibold text-slate-400">FC</span></p>
    </div>

    <dl class="mt-5 grid grid-cols-2 gap-4 text-sm">
        <div>
            <dt class="text-xs font-medium text-slate-400">Début</dt>
            <dd class="mt-1 font-semibold text-slate-700">{{ $budget->date_debut->format('d/m/Y') }}</dd>
        </div>
        <div>
            <dt class="text-xs font-medium text-slate-400">Fin</dt>
            <dd class="mt-1 font-semibold text-slate-700">{{ $budget->date_fin->format('d/m/Y') }}</dd>
        </div>
    </dl>

    <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4">
        <a href="{{ route('budgets.show', $budget) }}" class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">Voir les détails</a>
        <div class="flex items-center gap-1">
            <a href="{{ route('budgets.edit', $budget) }}" aria-label="Modifier {{ $budget->periode }}" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-indigo-50 hover:text-indigo-600">
                <i class="fa-solid fa-pen-to-square"></i>
            </a>
            <form action="{{ route('budgets.destroy', $budget) }}" method="POST" data-delete-budget="{{ $budget->periode }}">
                @csrf
                @method('DELETE')
                <button type="submit" aria-label="Supprimer {{ $budget->periode }}" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-rose-50 hover:text-rose-600">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </form>
        </div>
    </div>
</article>
