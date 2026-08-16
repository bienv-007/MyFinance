@extends('layouts.app')

@section('title', 'Notifications')
@section('page_title', 'Notifications')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <p class="text-sm font-medium text-indigo-600">Centre de notifications</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">Restez informé</h2>
            </div>
            @if ($notifications->isNotEmpty())
                <div class="flex gap-3">
                    <form action="{{ route('notifications.read-all') }}" method="POST">@csrf @method('PATCH')<button class="rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50">Tout lire</button></form>
                    <form action="{{ route('notifications.destroy-all') }}" method="POST" data-delete-notifications>@csrf @method('DELETE')<button class="rounded-2xl bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-100">Tout supprimer</button></form>
                </div>
            @endif
        </div>

        @forelse ($notifications as $notification)
            <article class="flex gap-4 rounded-3xl border p-5 shadow-sm {{ $notification->est_lue ? 'border-slate-200 bg-white' : 'border-indigo-100 bg-indigo-50/50' }}">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ str_contains($notification->type, 'depense') ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600' }}"><i class="fa-solid {{ str_contains($notification->type, 'depense') ? 'fa-receipt' : 'fa-circle-check' }}"></i></div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-start justify-between gap-2"><a href="{{ route('notifications.show', $notification) }}" class="font-bold text-slate-950 hover:text-indigo-600">{{ $notification->titre }}</a><time class="text-xs text-slate-400">{{ $notification->date_notification->format('d/m/Y H:i') }}</time></div>
                    <p class="mt-1 text-sm leading-6 text-slate-600">{{ $notification->contenu }}</p>
                    @unless ($notification->est_lue)<span class="mt-3 inline-flex rounded-full bg-indigo-100 px-2.5 py-1 text-xs font-semibold text-indigo-700">Non lue</span>@endunless
                </div>
                <form action="{{ route('notifications.destroy', $notification) }}" method="POST" data-delete-notification>@csrf @method('DELETE')<button aria-label="Supprimer la notification" class="text-slate-400 hover:text-rose-600"><i class="fa-solid fa-trash"></i></button></form>
            </article>
        @empty
            <section class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center"><div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-50 text-2xl text-indigo-500"><i class="fa-regular fa-bell"></i></div><h2 class="mt-5 text-xl font-bold text-slate-950">Aucune notification</h2><p class="mt-2 text-sm text-slate-500">Les événements importants de votre gestion financière apparaîtront ici.</p></section>
        @endforelse

        @if ($notifications->hasPages())<div class="flex justify-center">{{ $notifications->links() }}</div>@endif
    </div>
@endsection
