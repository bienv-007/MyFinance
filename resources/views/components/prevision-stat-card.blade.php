@props(['label', 'value', 'icon', 'tone' => 'indigo', 'hint' => null])

@php
    $tones = [
        'indigo' => 'bg-indigo-50 text-indigo-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'violet' => 'bg-violet-50 text-violet-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'rose' => 'bg-rose-50 text-rose-600',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
@endphp

<article class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/40">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="truncate text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-3 truncate text-2xl font-bold tracking-tight text-slate-950">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1 truncate text-xs text-slate-400">{{ $hint }}</p>
            @endif
        </div>
        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $tones[$tone] ?? $tones['indigo'] }}">
            <i class="{{ $icon }}"></i>
        </div>
    </div>
</article>
