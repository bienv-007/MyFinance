@props(['label', 'value', 'icon', 'tone' => 'indigo', 'hint' => null])

@php
    $tones = [
        'indigo' => ['box' => 'bg-indigo-50 text-indigo-600', 'value' => 'text-slate-950'],
        'violet' => ['box' => 'bg-violet-50 text-violet-600', 'value' => 'text-slate-950'],
        'emerald' => ['box' => 'bg-emerald-50 text-emerald-600', 'value' => 'text-slate-950'],
        'amber' => ['box' => 'bg-amber-50 text-amber-600', 'value' => 'text-slate-950'],
        'rose' => ['box' => 'bg-rose-50 text-rose-600', 'value' => 'text-slate-950'],
        'slate' => ['box' => 'bg-slate-100 text-slate-600', 'value' => 'text-slate-950'],
    ];
    $toneClasses = $tones[$tone] ?? $tones['indigo'];
@endphp

<article class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/40">
    <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl {{ $toneClasses['box'] }}"><i class="{{ $icon }}"></i></span>
    </div>
    <p class="mt-4 truncate text-2xl font-bold tracking-tight {{ $toneClasses['value'] }}">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 truncate text-xs font-medium text-slate-400">{{ $hint }}</p>
    @endif
</article>
