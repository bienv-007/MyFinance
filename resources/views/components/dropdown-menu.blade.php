@props(['id' => ''])

<div x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false" class="relative inline-flex">
    <button @click="open = !open" type="button" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Actions">
        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
    </button>
    <div x-cloak x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        @click="open = false"
        class="absolute right-0 z-50 mt-1 w-48 origin-top-right rounded-2xl border border-slate-100 bg-white p-1.5 shadow-xl">
        {{ $slot }}
    </div>
</div>
