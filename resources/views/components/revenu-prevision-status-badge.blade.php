@props(['statut'])

@php
    $classes = match ($statut) {
        'Réalisée' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/10',
        "Aujourd'hui" => 'bg-indigo-50 text-indigo-700 ring-indigo-600/10',
        'À venir' => 'bg-amber-50 text-amber-700 ring-amber-600/10',
        default => 'bg-rose-50 text-rose-700 ring-rose-600/10',
    };
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $classes }}">
    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
    {{ $statut }}
</span>
