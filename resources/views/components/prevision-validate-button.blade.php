@props(['prevision', 'compact' => false])

<form action="{{ route('depense-previsions.validate', $prevision) }}" method="POST" data-validate-prevision="{{ $prevision->description }}" class="{{ $compact ? '' : 'inline-flex' }}">
    @csrf
    <button type="submit" aria-label="Valider la prévision" class="{{ $compact ? 'flex h-9 w-9 items-center justify-center rounded-xl' : 'inline-flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3' }} bg-emerald-50 font-semibold text-emerald-700 transition hover:bg-emerald-100">
        <i class="fa-solid fa-check"></i>
        @unless ($compact)
            Valider et enregistrer
        @endunless
    </button>
</form>
