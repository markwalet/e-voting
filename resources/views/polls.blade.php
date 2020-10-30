@extends('layouts.base')

@php
$user = Auth::user();
@endphp

@section('content')
<h1 class="font-title font-normal text-2xl">
    Welkom bij <span class="font-bold">Gumbo Millennium e-voting</span>
</h1>

<p class="text-lg mb-4">
    Hieronder zie je de voorstellen die op dit moment open zijn.
</p>

{{-- Notice if proxied --}}
<livewire:home-rights-card />

{{-- render polls --}}
<livewire:poll-vote-list />

{{-- done --}}
@endsection
