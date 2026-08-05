@props([
    'prevision',
    'categories',
    'action',
    'method' => 'POST',
    'submitLabel' => 'Enregistrer la prévision',
])

@php
    $datePrevision = old('date_previsionnelle', $prevision?->date_previsionnelle?->format('Y-m-d'));
@endphp

<form action="{{ $action }}" method="POST" class="space-y-8" novalidate>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-6 md:grid-cols-2">
        <div>
            <label for="id_categorie" class="mb-2 block text-sm font-semibold text-slate-700">Catégorie</label>
            <select id="id_categorie" name="id_categorie" required
                class="block w-full rounded-2xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ $errors->has('id_categorie') ? 'border-rose-400 ring-4 ring-rose-50' : 'border-slate-200' }}">
                <option value="">Sélectionner une catégorie</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id_categorie }}" @selected((string) old('id_categorie', $prevision?->id_categorie) === (string) $category->id_categorie)>{{ $category->nom_categorie }}</option>
                @endforeach
            </select>
            @error('id_categorie')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-rose-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="montant_previsionnel" class="mb-2 block text-sm font-semibold text-slate-700">Montant prévu</label>
            <div class="relative">
                <input id="montant_previsionnel" name="montant_previsionnel" type="number" value="{{ old('montant_previsionnel', $prevision?->montant_previsionnel) }}"
                    min="0.01" step="0.01" placeholder="0,00" required
                    class="block w-full rounded-2xl border bg-white px-4 py-3.5 pr-14 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ $errors->has('montant_previsionnel') ? 'border-rose-400 ring-4 ring-rose-50' : 'border-slate-200' }}">
                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-semibold text-slate-400">FC</span>
            </div>
            @error('montant_previsionnel')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-rose-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date_previsionnelle" class="mb-2 block text-sm font-semibold text-slate-700">Date prévue</label>
            <input id="date_previsionnelle" name="date_previsionnelle" type="date" value="{{ $datePrevision }}" required
                class="block w-full rounded-2xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ $errors->has('date_previsionnelle') ? 'border-rose-400 ring-4 ring-rose-50' : 'border-slate-200' }}">
            @error('date_previsionnelle')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-rose-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Description</label>
            <textarea id="description" name="description" rows="4" maxlength="2000" required placeholder="Ex. Achat de fournitures pour la rentrée..."
                class="block w-full resize-none rounded-2xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 {{ $errors->has('description') ? 'border-rose-400 ring-4 ring-rose-50' : 'border-slate-200' }}">{{ old('description', $prevision?->description) }}</textarea>
            @error('description')
                <p class="mt-2 flex items-center gap-2 text-sm font-medium text-rose-600"><i class="fa-solid fa-circle-exclamation"></i>{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-6 sm:flex-row sm:justify-end">
        <a href="{{ route('depense-previsions.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Annuler</a>
        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-950/10 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
            <i class="fa-solid fa-check"></i>
            {{ $submitLabel }}
        </button>
    </div>
</form>
