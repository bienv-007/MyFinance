@extends('layouts.app')

@section('title', 'Notification')
@section('page_title', 'Notification')

@section('content')
    <div class="mx-auto max-w-3xl"><a href="{{ route('notifications.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800"><i class="fa-solid fa-arrow-left mr-2"></i>Retour aux notifications</a><article class="mt-5 rounded-3xl border border-slate-200 bg-white p-7 shadow-sm"><p class="text-sm text-slate-400">{{ $notification->date_notification->format('d/m/Y à H:i') }}</p><h2 class="mt-2 text-2xl font-bold text-slate-950">{{ $notification->titre }}</h2><p class="mt-5 leading-7 text-slate-600">{{ $notification->contenu }}</p></article></div>
@endsection
