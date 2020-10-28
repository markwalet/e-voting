@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Ledennbeheer
</h1>

<p class="text-lg mb-4">
    Hieronder staan de leden in het systeem.
</p>

<livewire:admin-user-list />

@endsection
