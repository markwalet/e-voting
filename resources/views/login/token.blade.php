@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Check je telefoon
</h1>

<p class="text-lg">
    Er is een SMS'je gestuurt naar het bij Gumbo bekende nummer. Typ hieronder de 8 cijferige code in.
</p>

<form action="{{ route('login.verify') }}" method="post">
    @csrf
    {{-- Token --}}
    <div class="form-field">
        <label for="token" class="form-field__label">Toegangscode</label>
        <input type="text" pattern="[0-9]{8}" class="form-field__input form-input" name="token" id="token" />
    </div>

    {{-- Submit --}}
    <div class="form-field">
        <button class="btn btn--brand">Versturen</button>
    </div>
</form>

<form action="{{ route('login.retry') }}" method="post" id="resend-form">@csrf</form>

<p>Geen code ontvangen? <a data-submit="resend-form" href="{{ route('login.retry') }}">Stuur 'm dan opnieuw</a>.</p>

@endsection
