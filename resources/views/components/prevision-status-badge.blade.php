@props(['statut'])

@php
    $style = match ($statut) {
        "Aujourd'hui" => [
            'classes' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
            'icon' => 'fa-solid fa-bolt',
        ],
        'À venir' => [
            'classes' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
            'icon' => 'fa-regular fa-clock',
        ],
        default => [
            'classes' => 'bg-rose-50 text-rose-700 ring-rose-600/20',
            'icon' => 'fa-solid fa-clock-rotate-left',
        ],
    };
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $style['classes'] }}">
    <i class="{{ $style['icon'] }} text-[10px]"></i>
    {{ $statut }}
</span>
