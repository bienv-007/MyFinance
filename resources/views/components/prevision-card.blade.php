@props(['prevision'])

<article class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/40">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="truncate text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">{{ $prevision->categorie?->nom_categorie ?? 'Sans catégorie' }}</p>
            <h3 class="mt-1 truncate text-lg font-bold text-slate-950">{{ $prevision->description }}</h3>
        </div>
        <x-prevision-status-badge :statut="$prevision->statut" />
    </div>
    <p class="mt-6 text-2xl font-bold tracking-tight text-slate-950">{{ number_format((float) $prevision->montant_previsionnel, 2, ',', ' ') }} <span class="text-sm font-semibold text-slate-400">FC</span></p>
    <p class="mt-2 flex items-center gap-2 text-sm text-slate-500"><i class="fa-regular fa-calendar"></i>{{ $prevision->date_previsionnelle->format('d/m/Y') }}</p>
    <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
        <a href="{{ route('depense-previsions.show', $prevision) }}" class="text-sm font-semibold text-indigo-600 transition hover:text-indigo-800">Voir le détail</a>
        <x-dropdown-menu>
            <x-dropdown-item href="{{ route('depense-previsions.edit', $prevision) }}" icon="fa-solid fa-pen-to-square">Modifier</x-dropdown-item>
            @if ($prevision->statut !== 'Réalisée')
                <x-dropdown-item href="{{ route('depense-previsions.validate', $prevision) }}" method="POST" as="form" icon="fa-solid fa-check" danger data-validate-prevision="{{ $prevision->description }}">Valider</x-dropdown-item>
            @endif
            <div class="my-1 border-t border-slate-100"></div>
            <x-dropdown-item href="{{ route('depense-previsions.destroy', $prevision) }}" method="DELETE" as="form" icon="fa-solid fa-trash-can" danger data-delete-prevision="{{ $prevision->description }}">Supprimer</x-dropdown-item>
        </x-dropdown-menu>
    </div>
</article>
