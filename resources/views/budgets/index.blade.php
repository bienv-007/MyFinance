@extends('layouts.app')

@section('title', 'Mes budgets')
@section('page_title', 'Mes budgets')

@section('content')
    <div class="mx-auto max-w-[1500px] space-y-8">
        <div class="flex flex-col justify-between gap-5 sm:flex-row sm:items-end">
            <div>
                <p class="text-sm font-medium text-indigo-600">Pilotage financier</p>
                <h2 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">Planifiez avec clarté.</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Gardez une vue simple et précise de vos enveloppes mensuelles.</p>
            </div>
            @if ($stats['total'] === 0)
                <a href="{{ route('budgets.create') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <i class="fa-solid fa-plus"></i>
                    Nouveau budget
                </a>
            @endif
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <x-budget-stat-card label="Budgets créés" :value="number_format($stats['total'], 0, ',', ' ')" icon="fa-solid fa-layer-group" tone="indigo" hint="Depuis le début" />
            <x-budget-stat-card label="Budget actif" :value="number_format($stats['actifs'], 0, ',', ' ')" icon="fa-solid fa-bolt" tone="emerald" hint="Sur la période actuelle" />
            <x-budget-stat-card label="Montant budgété" :value="number_format($stats['montant_total'], 2, ',', ' ') . ' FC'" icon="fa-solid fa-coins" tone="violet" hint="Cumul de vos budgets" />
        </div>

        <section class="rounded-3xl border border-slate-200/80 bg-white p-4 shadow-sm shadow-slate-200/40 sm:p-5">
            <form method="GET" action="{{ route('budgets.index') }}" class="flex flex-col gap-3 xl:flex-row xl:items-center">
                <div class="relative min-w-0 flex-1">
                    <label for="search" class="sr-only">Rechercher un budget</label>
                    <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                    <input id="search" type="search" name="search" value="{{ $search }}" placeholder="Rechercher par période, montant ou date..."
                        class="block w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                </div>
                <div class="grid grid-cols-2 gap-3 sm:flex">
                    <label class="sr-only" for="sort">Trier par</label>
                    <select id="sort" name="sort" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-600 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                        <option value="date_debut" @selected($sort === 'date_debut')>Date de début</option>
                        <option value="date_fin" @selected($sort === 'date_fin')>Date de fin</option>
                        <option value="periode" @selected($sort === 'periode')>Période</option>
                        <option value="montant_total" @selected($sort === 'montant_total')>Montant</option>
                    </select>
                    <label class="sr-only" for="direction">Ordre</label>
                    <select id="direction" name="direction" class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-600 outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
                        <option value="desc" @selected($direction === 'desc')>Décroissant</option>
                        <option value="asc" @selected($direction === 'asc')>Croissant</option>
                    </select>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700 sm:flex-none">
                        <i class="fa-solid fa-filter"></i>
                        Filtrer
                    </button>
                    @if ($search !== '')
                        <a href="{{ route('budgets.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-500 transition hover:bg-slate-50" aria-label="Réinitialiser la recherche">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>
        </section>

        @if ($budgets->isEmpty())
            <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center shadow-sm">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-50 text-2xl text-indigo-500">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h2 class="mt-6 text-xl font-bold text-slate-950">{{ $search !== '' ? 'Aucun budget trouvé' : 'Votre espace budget est prêt' }}</h2>
                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    {{ $search !== '' ? 'Essayez une autre recherche ou réinitialisez les filtres.' : 'Créez votre premier budget pour commencer à suivre vos objectifs mensuels.' }}
                </p>
                @if ($search !== '')
                    <a href="{{ route('budgets.index') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">Réinitialiser la recherche <i class="fa-solid fa-arrow-right"></i></a>
                @else
                    <a href="{{ route('budgets.create') }}" class="mt-6 inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">Créer un budget <i class="fa-solid fa-arrow-right"></i></a>
                @endif
            </section>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:hidden">
                @foreach ($budgets as $budget)
                    <x-budget-card :budget="$budget" />
                @endforeach
            </div>

            <section class="hidden overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/40 lg:block">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50/70 text-xs uppercase tracking-[0.12em] text-slate-400">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">Période</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Montant total</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Début</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Fin</th>
                                <th scope="col" class="px-6 py-4 font-semibold">Statut</th>
                                <th scope="col" class="px-6 py-4 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($budgets as $budget)
                                <tr class="group transition hover:bg-slate-50/70">
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <a href="{{ route('budgets.show', $budget) }}" class="font-bold text-slate-900 transition hover:text-indigo-600">{{ $budget->periode }}</a>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5 font-semibold text-slate-700">{{ number_format((float) $budget->montant_total, 2, ',', ' ') }} <span class="text-xs font-medium text-slate-400">FC</span></td>
                                    <td class="whitespace-nowrap px-6 py-5 text-slate-500">{{ $budget->date_debut->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-6 py-5 text-slate-500">{{ $budget->date_fin->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-6 py-5"><x-budget-status-badge :statut="$budget->statut" /></td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <div class="flex justify-end opacity-70 transition group-hover:opacity-100">
                                            <x-dropdown-menu>
                                                <x-dropdown-item href="{{ route('budgets.show', $budget) }}" icon="fa-regular fa-eye">Voir les détails</x-dropdown-item>
                                                <x-dropdown-item href="{{ route('budgets.edit', $budget) }}" icon="fa-solid fa-pen-to-square">Modifier</x-dropdown-item>
                                                <div class="my-1 border-t border-slate-100"></div>
                                                <x-dropdown-item href="{{ route('budgets.destroy', $budget) }}" method="DELETE" as="form" icon="fa-solid fa-trash-can" danger data-delete-budget="{{ $budget->periode }}">Supprimer</x-dropdown-item>
                                            </x-dropdown-menu>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($budgets->hasPages())
                <div class="flex justify-center pt-1">
                    {{ $budgets->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
