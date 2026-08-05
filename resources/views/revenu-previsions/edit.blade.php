@extends('layouts.app')

@section('title', 'Modifier la prévision de revenu')
@section('page_title', 'Modifier la prévision de revenu')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-8 flex items-center gap-3"><a href="{{ route('revenu-previsions.index') }}" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:border-emerald-200 hover:text-emerald-600" aria-label="Retour"><i class="fa-solid fa-arrow-left"></i></a><div><p class="text-sm font-medium text-emerald-600">Mise à jour</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Ajustez cette prévision.</h2></div></div>
        <section class="rounded-3xl border border-slate-200/80 bg-white p-7 shadow-sm shadow-slate-200/40 sm:p-10"><div class="mb-7 flex items-start justify-between gap-4"><div><h3 class="text-xl font-bold text-slate-950">Détails du revenu prévu</h3><p class="mt-1 text-sm text-slate-500">Les changements seront pris en compte dans vos statistiques.</p></div><x-revenu-prevision-status-badge :statut="$prevision->statut" /></div><form action="{{ route('revenu-previsions.update', $prevision) }}" method="POST">@csrf @method('PUT')<x-revenu-prevision-form :prevision="$prevision" submit-label="Enregistrer les changements" /></form></section>
    </div>
@endsection
