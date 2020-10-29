@extends('layouts.base')

@php
$user = Auth::user();
@endphp

@section('content')
<h1 class="font-title font-bold text-2xl">
    Welkom bij Gumbo Millennium <span class="font-normal">e-voting</span>
</h1>

<p class="text-lg mb-4">
    Hieronder zie je de voorstellen die op dit moment open zijn.
</p>

{{-- Notice if proxied --}}
@cannot('vote')
<div class="notice notice--warning">
    <strong class="notice__title">Je mag niet stemmen</strong>
@if (!$user->is_voter)
    Je hebt geen stemrecht op deze ALV.
@elseif ($user->is_voter && !$user->is_present)
    Je bent niet aangemeld, meld je eerst aan bij het bestuur.
@elseif ($user->proxy !== null)
    Je hebt {{ $user->proxy->name }} gemachtigd. Meld je bij het bestuur
    om de machtiging in te trekken.
@else
    We weten niet waarom
@endif
</div>
@endcan

{{-- render polls --}}
@forelse ($polls as $poll)
<livewire:poll-vote-card :poll="$poll" />
@empty
<div class="notice notice--info">
    Er zijn momenteel geen actieve voorstellen.
</div>
@endforelse

{{-- done --}}
@endsection
