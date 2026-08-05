@props(['prevision'])

<article class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/40">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="truncate text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Source prévue</p>
            <h3 class="mt-1 truncate text-lg font-bold text-slate-950">{{ $prevision->source_previsionnelle }}</h3>
        </div>
        <x-revenu-prevision-status-badge :statut="$prevision->statut" />
    </div>
    <p class="mt-6 text-2xl font-bold tracking-tight text-slate-950">{{ number_format((float) $prevision->montant_previsionnel, 2, ',', ' ') }} <span class="text-sm font-semibold text-slate-400">FC</span></p>
    <p class="mt-2 flex items-center gap-2 text-sm text-slate-500"><i class="fa-regular fa-calendar"></i>{{ $prevision->date_previsionnelle->format('d/m/Y') }}</p>
    <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-500">{{ $prevision->description }}</p>
    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
        <a href="{{ route('revenu-previsions.show', $prevision) }}" class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">Voir le détail</a>
        <div class="flex items-center gap-1">
            @if ($prevision->statut !== 'Réalisée')
                <x-revenu-prevision-receive-button :prevision="$prevision" compact />
            @endif
            <a href="{{ route('revenu-previsions.edit', $prevision) }}" aria-label="Modifier la prévision" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-indigo-50 hover:text-indigo-600"><i class="fa-solid fa-pen-to-square"></i></a>
            <form action="{{ route('revenu-previsions.destroy', $prevision) }}" method="POST" data-delete-revenu-prevision="{{ $prevision->source_previsionnelle }}">
                @csrf
                @method('DELETE')
                <button type="submit" aria-label="Supprimer la prévision" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"><i class="fa-solid fa-trash-can"></i></button>
            </form>
        </div>
    </div>
</article>
