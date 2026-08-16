@extends('layouts.app')

@section('title', 'Historique des budgets')
@section('page_title', 'Historique des budgets')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div><a href="{{ route('budgets.show', $budget) }}" class="text-sm font-semibold text-indigo-600"><i class="fa-solid fa-arrow-left mr-2"></i>Retour au budget</a><h2 class="mt-3 text-2xl font-bold text-slate-950">Cycles archivés</h2><p class="mt-1 text-sm text-slate-500">Chaque modification démarre un nouveau cycle de dépenses.</p></div>
        @forelse ($historiques as $historique)
            <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"><div class="flex flex-wrap justify-between gap-3"><div><h3 class="font-bold text-slate-950">{{ $historique->periode }}</h3><p class="mt-1 text-sm text-slate-500">Archivé le {{ $historique->date_archivage->format('d/m/Y H:i') }}</p></div><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">Terminé</span></div><dl class="mt-5 grid gap-4 sm:grid-cols-3"><div><dt class="text-xs uppercase text-slate-400">Montant initial</dt><dd class="mt-1 font-bold">{{ number_format((float) $historique->montant_total, 2, ',', ' ') }} FC</dd></div><div><dt class="text-xs uppercase text-slate-400">Dépensé</dt><dd class="mt-1 font-bold text-rose-600">{{ number_format((float) $historique->montant_depense, 2, ',', ' ') }} FC</dd></div><div><dt class="text-xs uppercase text-slate-400">Solde final</dt><dd class="mt-1 font-bold text-emerald-600">{{ number_format((float) $historique->solde_final, 2, ',', ' ') }} FC</dd></div></dl><div class="mt-6 grid gap-5 lg:grid-cols-2"><div><h4 class="font-semibold text-slate-900">Dépenses ({{ $historique->depenses->count() }})</h4><ul class="mt-2 space-y-2 text-sm text-slate-600">@forelse($historique->depenses as $depense)<li class="rounded-xl bg-rose-50 px-3 py-2">{{ $depense->description ?: 'Dépense' }} — {{ number_format((float) $depense->montant, 2, ',', ' ') }} FC</li>@empty<li>Aucune dépense.</li>@endforelse</ul></div><div><h4 class="font-semibold text-slate-900">Revenus ({{ $historique->revenus->count() }})</h4><ul class="mt-2 space-y-2 text-sm text-slate-600">@forelse($historique->revenus as $revenu)<li class="rounded-xl bg-emerald-50 px-3 py-2">{{ $revenu->source }} — {{ number_format((float) $revenu->montant, 2, ',', ' ') }} FC</li>@empty<li>Aucun revenu.</li>@endforelse</ul></div></div></article>
        @empty
            <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-sm text-slate-500">Aucun cycle de budget archivé.</section>
        @endforelse
        @if ($historiques->hasPages())<div class="flex justify-center">{{ $historiques->links() }}</div>@endif
    </div>
@endsection
