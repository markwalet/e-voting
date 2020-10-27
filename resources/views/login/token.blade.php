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
    <label for="token">Toegangscode</label>
    <input value="{{ old('email') }}" type="text" pattern="[0-9]{8}" class="rounded border-gray-primary-3 p-2" name="token" id="token" />
</form>

<form action="{{ route('login.retry') }}" method="post" id="resend-form">@csrf</form>

<p>Geen code ontvangen? <a data-submit="resend-form" href="{{ route('login.retry') }}">Stuur 'm dan opnieuw</a>.</p>

@endsection
