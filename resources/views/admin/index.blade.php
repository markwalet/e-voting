@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Administratiepaneel
</h1>

<p class="text-lg">
    Kies een optie
</p>

<div class="flex items-center justify-stretch w-full">
    <a href="{{ route('admin.users.index') }}" class="btn btn--brand mr-4">Leden</a>
    <a href="{{ route('admin.polls.index') }}" class="btn btn--brand">Voorstellen</a>
</div>

@endsection
