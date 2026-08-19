@props(['href' => null, 'method' => null, 'as' => 'link', 'danger' => false, 'icon' => null])

@if ($as === 'link' && $href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => ($danger ? 'text-rose-600 hover:bg-rose-50' : 'text-slate-700 hover:bg-slate-50') . ' flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition']) }}>
        @if ($icon)
            <i class="{{ $icon }} w-4 text-center text-xs"></i>
        @endif
        {{ $slot }}
    </a>
@elseif ($as === 'button')
    <button type="button" {{ $attributes->merge(['class' => ($danger ? 'text-rose-600 hover:bg-rose-50' : 'text-slate-700 hover:bg-slate-50') . ' flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition']) }}>
        @if ($icon)
            <i class="{{ $icon }} w-4 text-center text-xs"></i>
        @endif
        {{ $slot }}
    </button>
@elseif ($as === 'form')
    <form {{ $attributes->merge(['action' => $href, 'method' => 'POST', 'class' => 'w-full']) }}>
        @csrf
        @if ($method)
            @method($method)
        @endif
        <button type="submit" class="{{ $danger ? 'text-rose-600 hover:bg-rose-50' : 'text-slate-700 hover:bg-slate-50' }} flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-sm font-medium transition">
            @if ($icon)
                <i class="{{ $icon }} w-4 text-center text-xs"></i>
            @endif
            {{ $slot }}
        </button>
    </form>
@endif
