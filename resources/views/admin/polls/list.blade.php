@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Beheer <strong class="font-bold">voorstellen</strong>
</h1>

<p class="text-lg">
    Hieronder zie je de voorstellen die in dit systeem staan. Ook deze kan je filteren.
</p>

<livewire:admin-poll-list />

@include('admin.polls.create')

@endsection
