@extends('layouts.app')

@section('title', 'Nouvelle prévision de revenu')
@section('page_title', 'Nouvelle prévision de revenu')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-8 flex items-center gap-3">
            <a href="{{ route('revenu-previsions.index') }}" class="flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:border-emerald-200 hover:text-emerald-600" aria-label="Retour"><i class="fa-solid fa-arrow-left"></i></a>
            <div><p class="text-sm font-medium text-emerald-600">Planification</p><h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Ajoutez un revenu à venir.</h2></div>
        </div>
        <section class="grid overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm shadow-slate-200/40 lg:grid-cols-[0.8fr_1.2fr]">
            <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 p-7 text-white sm:p-10"><span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/20 text-emerald-300"><i class="fa-solid fa-arrow-trend-up text-xl"></i></span><h3 class="mt-8 text-2xl font-bold">Anticipez vos entrées.</h3><p class="mt-3 text-sm leading-7 text-slate-300">Un revenu prévu et bien daté vous aide à préparer vos dépenses et à sécuriser votre équilibre financier.</p><div class="mt-10 space-y-4 text-sm text-slate-300"><p class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-300"></i>Montant strictement positif</p><p class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-300"></i>Source et description obligatoires</p><p class="flex items-center gap-3"><i class="fa-solid fa-check text-emerald-300"></i>Statut calculé automatiquement</p></div></div>
            <form action="{{ route('revenu-previsions.store') }}" method="POST" class="p-7 sm:p-10">@csrf<x-revenu-prevision-form :prevision="$prevision" submit-label="Créer la prévision" /></form>
        </section>
    </div>
@endsection
