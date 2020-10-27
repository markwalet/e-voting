@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Inloggen
</h1>

<p class="text-lg">
    Typ hieronder je e-mailadres in. De accounts zijn opgesteld uit
    de ledenadministratie, dus je moet het e-mailadres gebruiken
    dat bekend is bij het bestuur.
</p>

<form action="{{ route('login') }}" method="post">
    @csrf
    <label for="email">E-mailadres</label>
    <input value="{{ old('email') }}" type="email" class="rounded border-gray-primary-3 p-2" name="email" id="email" />
</form>

@endsection
