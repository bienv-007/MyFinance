@props(['prevision', 'compact' => false])

<form action="{{ route('revenu-previsions.receive', $prevision) }}" method="POST" data-receive-revenu-prevision="{{ $prevision->source_previsionnelle }}" class="{{ $compact ? '' : 'inline-flex' }}">
    @csrf
    <button type="submit" aria-label="Marquer le revenu comme perçu" class="{{ $compact ? 'flex h-9 w-9 items-center justify-center rounded-xl' : 'inline-flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3' }} bg-emerald-50 font-semibold text-emerald-700 transition hover:bg-emerald-100">
        <i class="fa-solid fa-hand-holding-dollar"></i>
        @unless ($compact)
            Marquer comme perçu
        @endunless
    </button>
</form>
