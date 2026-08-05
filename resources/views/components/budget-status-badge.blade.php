@props(['statut'])

@php
    $style = match ($statut) {
        'À venir' => [
            'classes' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            'icon' => 'fa-regular fa-clock',
        ],
        'En cours' => [
            'classes' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            'icon' => 'fa-solid fa-circle-play',
        ],
        default => [
            'classes' => 'bg-slate-100 text-slate-600 ring-slate-500/20',
            'icon' => 'fa-solid fa-circle-check',
        ],
    };
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $style['classes'] }}">
    <i class="{{ $style['icon'] }} text-[10px]"></i>
    {{ $statut }}
</span>
