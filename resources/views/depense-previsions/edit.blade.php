@extends('layouts.app')

@section('title', 'Modifier la prévision')
@section('page_title', 'Modifier la prévision')

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <div class="flex items-center gap-3"><a href="{{ route('depense-previsions.show', $prevision) }}" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:border-indigo-200 hover:text-indigo-600" aria-label="Retour"><i class="fa-solid fa-arrow-left"></i></a><div><p class="text-sm font-medium text-indigo-600">Mise à jour</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Modifier la prévision</h2></div></div>
        <section class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-sm shadow-slate-200/40 sm:p-8"><div class="mb-8 flex flex-col justify-between gap-3 border-b border-slate-100 pb-6 sm:flex-row sm:items-start"><div><h3 class="text-lg font-bold text-slate-950">Détails de la dépense future</h3><p class="mt-1 text-sm text-slate-500">Actualisez les informations de cette prévision.</p></div><x-prevision-status-badge :statut="$prevision->statut" /></div><x-prevision-form :prevision="$prevision" :categories="$categories" :action="route('depense-previsions.update', $prevision)" method="PUT" submit-label="Enregistrer les modifications" /></section>
    </div>
@endsection
