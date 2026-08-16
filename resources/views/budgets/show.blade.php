@extends('layouts.app')

@section('title', $budget->periode)
@section('page_title', 'Détail du budget')

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('budgets.index') }}" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:border-indigo-200 hover:text-indigo-600" aria-label="Retour aux budgets">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <p class="text-sm font-medium text-indigo-600">Vue détaillée</p>
                    <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">{{ $budget->periode }}</h2>
                </div>
            </div>
            <div class="flex gap-3 pl-[52px] sm:pl-0">
                <a href="{{ route('budgets.edit', $budget) }}" class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-600 transition hover:border-indigo-200 hover:text-indigo-600 sm:flex-none">
                    <i class="fa-solid fa-pen-to-square"></i>
                    Modifier
                </a>
                <form action="{{ route('budgets.destroy', $budget) }}" method="POST" data-delete-budget="{{ $budget->periode }}" class="flex-1 sm:flex-none">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-600 transition hover:bg-rose-100">
                        <i class="fa-solid fa-trash-can"></i>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>

        <section class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/40">
            <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 px-6 py-8 text-white sm:px-10 sm:py-10">
                <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-start">
                    <div>
                        <p class="text-sm font-medium text-slate-300">Montant total budgété</p>
                        <p class="mt-3 text-4xl font-bold tracking-tight sm:text-5xl">{{ number_format((float) $budget->montant_total, 2, ',', ' ') }} <span class="text-xl font-semibold text-indigo-300">FC</span></p>
                    </div>
                    <x-budget-status-badge :statut="$budget->statut" />
                </div>
            </div>

            <div class="grid gap-6 p-6 sm:grid-cols-2 lg:grid-cols-3 sm:p-10">
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5">
                    <div class="flex items-center gap-3 text-emerald-600"><i class="fa-solid fa-wallet"></i><span class="text-xs font-semibold uppercase tracking-[0.16em]">Solde restant</span></div>
                    <p class="mt-3 text-lg font-bold text-slate-950">{{ number_format((float) $budget->solde, 2, ',', ' ') }} FC</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <div class="flex items-center gap-3 text-slate-400"><i class="fa-regular fa-calendar-plus"></i><span class="text-xs font-semibold uppercase tracking-[0.16em]">Date de début</span></div>
                    <p class="mt-3 text-lg font-bold text-slate-950">{{ $budget->date_debut->format('d/m/Y') }}</p>
                </div>
                <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">
                    <div class="flex items-center gap-3 text-slate-400"><i class="fa-regular fa-calendar-check"></i><span class="text-xs font-semibold uppercase tracking-[0.16em]">Date de fin</span></div>
                    <p class="mt-3 text-lg font-bold text-slate-950">{{ $budget->date_fin->format('d/m/Y') }}</p>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-indigo-100 bg-indigo-50/60 p-6 sm:p-8">
            <div class="flex gap-4">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white text-indigo-600 shadow-sm"><i class="fa-solid fa-chart-simple"></i></div>
                <div>
                    <h3 class="font-bold text-slate-950">Suivi de la période</h3>
                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        Ce budget est actuellement <strong>{{ strtolower($budget->statut) }}</strong>. Son statut évolue automatiquement selon les dates configurées.
                    </p>
                </div>
            </div>
        </section>
    </div>
@endsection
