@extends('layouts.app')

@section('title', 'Prévisions de revenus')
@section('page_title', 'Prévisions de revenus')

@section('content')
    <div class="mx-auto max-w-[1500px] space-y-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium text-emerald-600">Anticipation financière</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">Préparez vos entrées d’argent.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Planifiez vos revenus futurs et distinguez en un coup d’œil ce qui est attendu de ce qui est déjà perçu.</p>
            </div>
            <a href="{{ route('revenu-previsions.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                <i class="fa-solid fa-plus"></i>
                Nouvelle prévision
            </a>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <x-revenu-prevision-stat-card label="Total des prévisions" :value="number_format($stats['total'], 0, ',', ' ')" icon="fa-solid fa-list-check" tone="indigo" hint="Toutes les prévisions" />
            <x-revenu-prevision-stat-card label="Revenus prévus" :value="number_format($stats['montant_total'], 2, ',', ' ') . ' FC'" icon="fa-solid fa-coins" tone="violet" hint="Montant cumulé" />
            <x-revenu-prevision-stat-card label="Prochain revenu" :value="$stats['prochaine'] ? $stats['prochaine']->date_previsionnelle->format('d/m/Y') : 'Aucun'" icon="fa-solid fa-forward" tone="amber" :hint="$stats['prochaine']?->source_previsionnelle ?? 'Aucun revenu à venir'" />
            <x-revenu-prevision-stat-card label="Source principale" :value="$stats['source_principale'] ?? 'Aucune'" icon="fa-solid fa-chart-pie" tone="emerald" hint="Selon vos prévisions" />
            <x-revenu-prevision-stat-card label="Plus élevé" :value="$stats['plus_elevee'] ? number_format((float) $stats['plus_elevee']->montant_previsionnel, 2, ',', ' ') . ' FC' : 'Aucun'" icon="fa-solid fa-arrow-trend-up" tone="rose" :hint="$stats['plus_elevee']?->source_previsionnelle ?? 'Aucun montant enregistré'" />
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-revenu-prevision-stat-card label="Prévu ce mois" :value="number_format($stats['montant_mois'], 2, ',', ' ') . ' FC'" icon="fa-regular fa-calendar" tone="slate" />
            <x-revenu-prevision-stat-card label="Prévu cette année" :value="number_format($stats['montant_annee'], 2, ',', ' ') . ' FC'" icon="fa-solid fa-calendar-days" tone="slate" />
            <x-revenu-prevision-stat-card label="Revenus attendus" :value="number_format($stats['attendus'], 0, ',', ' ')" icon="fa-solid fa-hourglass-half" tone="amber" />
            <x-revenu-prevision-stat-card label="Prévisions expirées" :value="number_format($stats['expirees'], 0, ',', ' ')" icon="fa-solid fa-clock-rotate-left" tone="rose" />
        </div>

        <section class="rounded-3xl border border-slate-200/80 bg-white p-4 shadow-sm shadow-slate-200/40 sm:p-5">
            <form method="GET" action="{{ route('revenu-previsions.index') }}" class="space-y-3">
                <div class="flex flex-col gap-3 xl:flex-row xl:items-center">
                    <div class="relative min-w-0 flex-1">
                        <label for="search" class="sr-only">Rechercher</label>
                        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                        <input id="search" type="search" name="search" value="{{ $filters['search'] }}" placeholder="Rechercher par source, description ou montant..." class="block w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                    </div>
                    <input type="text" name="source" value="{{ $filters['source'] }}" placeholder="Source" aria-label="Filtrer par source" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                    <input type="month" name="mois" value="{{ $filters['mois'] }}" aria-label="Filtrer par mois" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                    <input type="date" name="date" value="{{ $filters['date'] }}" aria-label="Filtrer par date" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                </div>
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-end">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="number" name="montant_min" value="{{ $filters['montant_min'] }}" min="0" step="0.01" placeholder="Montant min." class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                        <input type="number" name="montant_max" value="{{ $filters['montant_max'] }}" min="0" step="0.01" placeholder="Montant max." class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                    </div>
                    <select name="sort" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-600 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                        <option value="date_previsionnelle" @selected($filters['sort'] === 'date_previsionnelle')>Trier par date</option>
                        <option value="montant_previsionnel" @selected($filters['sort'] === 'montant_previsionnel')>Trier par montant</option>
                        <option value="source_previsionnelle" @selected($filters['sort'] === 'source_previsionnelle')>Trier par source</option>
                    </select>
                    <select name="direction" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-600 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100">
                        <option value="asc" @selected($filters['direction'] === 'asc')>Croissant</option>
                        <option value="desc" @selected($filters['direction'] === 'desc')>Décroissant</option>
                    </select>
                    <div class="flex gap-3">
                        <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700 lg:flex-none"><i class="fa-solid fa-filter"></i>Filtrer</button>
                        @if (collect($filters)->except(['sort', 'direction'])->contains(fn ($value) => $value !== null && $value !== ''))
                            <a href="{{ route('revenu-previsions.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-500 transition hover:bg-slate-50" aria-label="Réinitialiser les filtres"><i class="fa-solid fa-xmark"></i></a>
                        @endif
                    </div>
                </div>
            </form>
        </section>

        @php
            $hasFilters = collect($filters)->except(['sort', 'direction'])->contains(fn ($value) => $value !== null && $value !== '');
        @endphp

        @if ($previsions->isEmpty())
            <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-emerald-50 text-2xl text-emerald-500"><i class="fa-solid fa-arrow-trend-up"></i></div>
                <h2 class="mt-6 text-xl font-bold text-slate-950">{{ $hasFilters ? 'Aucune prévision trouvée' : 'Votre plan de revenus est vide' }}</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ $hasFilters ? 'Modifiez vos filtres pour élargir la recherche.' : 'Ajoutez votre première entrée future pour mieux préparer votre budget.' }}</p>
                <a href="{{ route('revenu-previsions.create') }}" class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">Créer une prévision <i class="fa-solid fa-arrow-right"></i></a>
            </section>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:hidden">
                @foreach ($previsions as $prevision)
                    <x-revenu-prevision-card :prevision="$prevision" />
                @endforeach
            </div>

            <section class="hidden overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/40 lg:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] text-left text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50/70 text-xs uppercase tracking-[0.12em] text-slate-400">
                            <tr><th class="px-6 py-4 font-semibold">Source prévue</th><th class="px-6 py-4 font-semibold">Montant prévu</th><th class="px-6 py-4 font-semibold">Date prévue</th><th class="px-6 py-4 font-semibold">Description</th><th class="px-6 py-4 font-semibold">Statut</th><th class="px-6 py-4 text-right font-semibold">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($previsions as $prevision)
                                <tr class="group transition hover:bg-slate-50/70">
                                    <td class="whitespace-nowrap px-6 py-5 font-semibold text-slate-900">{{ $prevision->source_previsionnelle }}</td>
                                    <td class="whitespace-nowrap px-6 py-5 font-semibold text-slate-700">{{ number_format((float) $prevision->montant_previsionnel, 2, ',', ' ') }} <span class="text-xs font-medium text-slate-400">FC</span></td>
                                    <td class="whitespace-nowrap px-6 py-5 text-slate-500">{{ $prevision->date_previsionnelle->format('d/m/Y') }}</td>
                                    <td class="max-w-xs truncate px-6 py-5 text-slate-500" title="{{ $prevision->description }}">{{ $prevision->description }}</td>
                                    <td class="whitespace-nowrap px-6 py-5"><x-revenu-prevision-status-badge :statut="$prevision->statut" /></td>
                                    <td class="whitespace-nowrap px-6 py-5"><div class="flex justify-end gap-1 opacity-70 transition group-hover:opacity-100">@if ($prevision->statut !== 'Réalisée')<x-revenu-prevision-receive-button :prevision="$prevision" compact />@endif<a href="{{ route('revenu-previsions.show', $prevision) }}" aria-label="Voir la prévision" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-indigo-50 hover:text-indigo-600"><i class="fa-regular fa-eye"></i></a><a href="{{ route('revenu-previsions.edit', $prevision) }}" aria-label="Modifier la prévision" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-indigo-50 hover:text-indigo-600"><i class="fa-solid fa-pen-to-square"></i></a><form action="{{ route('revenu-previsions.destroy', $prevision) }}" method="POST" data-delete-revenu-prevision="{{ $prevision->source_previsionnelle }}">@csrf @method('DELETE')<button type="submit" aria-label="Supprimer la prévision" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-rose-50 hover:text-rose-600"><i class="fa-solid fa-trash-can"></i></button></form></div></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @if ($previsions->hasPages())
                <div class="flex justify-center pt-1">{{ $previsions->links() }}</div>
            @endif
        @endif
    </div>
@endsection
