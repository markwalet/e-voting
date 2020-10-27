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

<form action="{{ route('login') }}" method="post" class="my-4 flex flex-col items-stretch login__form">
    @csrf
    {{-- Email --}}
    <div class="form-field">
        <label for="email" class="form-field__label">E-mailadres</label>
        <input value="{{ old('email') }}" type="email" class="form-field__input form-input" name="email" id="email" />
    </div>

    {{-- Submit --}}
    <div class="form-field">
        <button class="btn btn--brand">Versturen</button>
    </div>
</form>

@endsection
