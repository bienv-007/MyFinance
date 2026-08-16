@props([
    'budget',
    'action',
    'method' => 'POST',
    'submitLabel' => 'Enregistrer le budget',
])

@php
    $dateDebut = old('date_debut', $budget?->date_debut?->format('Y-m-d'));
    $dateFin = old('date_fin', $budget?->date_fin?->format('Y-m-d'));
@endphp

<form action="{{ $action }}" method="POST" class="space-y-8" novalidate>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="periode" class="mb-2 block text-sm font-semibold text-slate-700">Période</label>
            <input id="periode" name="periode" type="text" value="{{ old('periode', $budget?->periode) }}"
                placeholder="Ex. Août 2026 ou 2026-08" maxlength="255" required
                class="block w-full rounded-2xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ $errors->has('periode') ? 'border-rose-400 ring-4 ring-rose-50' : 'border-slate-200' }}">
            @error('periode')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-rose-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="montant_total" class="mb-2 block text-sm font-semibold text-slate-700">Montant total</label>
            <div class="relative">
                <input id="montant_total" name="montant_total" type="number" value="{{ old('montant_total', $budget?->montant_total) }}"
                    min="0.01" step="0.01" placeholder="0,00" required
                    class="block w-full rounded-2xl border bg-white px-4 py-3.5 pr-14 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ $errors->has('montant_total') ? 'border-rose-400 ring-4 ring-rose-50' : 'border-slate-200' }}">
                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-semibold text-slate-400">FC</span>
            </div>
            @error('montant_total')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-rose-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date_debut" class="mb-2 block text-sm font-semibold text-slate-700">Date de début</label>
            <input id="date_debut" name="date_debut" type="date" value="{{ $dateDebut }}" required
                class="block w-full rounded-2xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ $errors->has('date_debut') ? 'border-rose-400 ring-4 ring-rose-50' : 'border-slate-200' }}">
            @error('date_debut')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-rose-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date_fin" class="mb-2 block text-sm font-semibold text-slate-700">Date de fin</label>
            <input id="date_fin" name="date_fin" type="date" value="{{ $dateFin }}" min="{{ $dateDebut }}" required
                class="block w-full rounded-2xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ $errors->has('date_fin') ? 'border-rose-400 ring-4 ring-rose-50' : 'border-slate-200' }}">
            @error('date_fin')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-rose-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
            @enderror
        </div>
    </div>

    @if ($method === 'PUT')
        <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-indigo-100 bg-indigo-50/60 p-4 text-sm text-slate-700">
            <input name="reinitialiser_solde" type="checkbox" value="1" @checked(old('reinitialiser_solde'))
                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
            <span>
                <span class="block font-semibold text-slate-900">Réinitialiser le solde</span>
                <span class="mt-0.5 block text-slate-500">Le solde restant sera fixé au nouveau montant total saisi.</span>
            </span>
        </label>
    @endif

    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
        <a href="{{ route('budgets.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
            Annuler
        </a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-950/10 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
            <i class="fa-solid fa-check"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>
