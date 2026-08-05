@props(['prevision', 'submitLabel'])

<div class="space-y-5">
    <div>
        <label for="source_previsionnelle" class="mb-1.5 block text-sm font-semibold text-slate-700">Source prévue</label>
        <input id="source_previsionnelle" name="source_previsionnelle" type="text" required maxlength="255" value="{{ old('source_previsionnelle', $prevision->source_previsionnelle) }}" placeholder="Ex. Salaire, bonus, activité..." class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
        @error('source_previsionnelle')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="montant_previsionnel" class="mb-1.5 block text-sm font-semibold text-slate-700">Montant prévu</label>
        <div class="relative">
            <input id="montant_previsionnel" name="montant_previsionnel" type="number" required min="0.01" step="0.01" value="{{ old('montant_previsionnel', $prevision->montant_previsionnel) }}" placeholder="0,00" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 pr-14 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-xs font-bold text-slate-400">FC</span>
        </div>
        @error('montant_previsionnel')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="date_previsionnelle" class="mb-1.5 block text-sm font-semibold text-slate-700">Date prévue</label>
        <input id="date_previsionnelle" name="date_previsionnelle" type="date" required value="{{ old('date_previsionnelle', $prevision->date_previsionnelle?->format('Y-m-d')) }}" class="block w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">
        @error('date_previsionnelle')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="description" class="mb-1.5 block text-sm font-semibold text-slate-700">Description</label>
        <textarea id="description" name="description" required maxlength="2000" rows="4" placeholder="Précisez l'origine ou le contexte de ce revenu..." class="block w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100">{{ old('description', $prevision->description) }}</textarea>
        @error('description')<p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="flex flex-col gap-3 pt-2 sm:flex-row">
        <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-950/10 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
            <i class="fa-solid fa-check"></i>
            {{ $submitLabel }}
        </button>
        <a href="{{ route('revenu-previsions.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-500 transition hover:bg-slate-50">Annuler</a>
    </div>
</div>
