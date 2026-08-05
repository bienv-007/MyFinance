@extends('layouts.app')

@section('title', 'Nouveau budget')
@section('page_title', 'Nouveau budget')

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('budgets.index') }}" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:border-indigo-200 hover:text-indigo-600" aria-label="Retour aux budgets">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <p class="text-sm font-medium text-indigo-600">Planification</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Créer un budget</h2>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_280px]">
            <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm shadow-slate-200/40 sm:p-8">
                <div class="mb-8 border-b border-slate-100 pb-6">
                    <h3 class="text-lg font-bold text-slate-950">Détails du budget</h3>
                    <p class="mt-1 text-sm text-slate-500">Définissez l’enveloppe et la période à suivre.</p>
                </div>
                <x-budget-form :budget="$budget" :action="route('budgets.store')" />
            </section>

            <aside class="h-fit rounded-3xl bg-slate-950 p-6 text-white shadow-xl shadow-slate-950/10">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-500/20 text-indigo-300"><i class="fa-solid fa-lightbulb"></i></div>
                <h3 class="mt-5 text-lg font-bold">Un budget utile</h3>
                <p class="mt-2 text-sm leading-6 text-slate-300">Choisissez une période suffisamment précise pour comparer facilement vos objectifs.</p>
                <ul class="mt-6 space-y-4 text-sm text-slate-300">
                    <li class="flex gap-3"><i class="fa-solid fa-check mt-0.5 text-emerald-400"></i><span>Le montant doit être supérieur à zéro.</span></li>
                    <li class="flex gap-3"><i class="fa-solid fa-check mt-0.5 text-emerald-400"></i><span>La date de fin doit suivre la date de début.</span></li>
                    <li class="flex gap-3"><i class="fa-solid fa-check mt-0.5 text-emerald-400"></i><span>Le statut se calcule automatiquement.</span></li>
                </ul>
            </aside>
        </div>
    </div>
@endsection
