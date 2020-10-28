@extends('layouts.base')

@php
$facts = [
    'Stemrecht' => $user->is_voter,
    'Machtigbaar' => $user->can_proxy,
    'Heeft machtiging afgegeven' => $user->proxy !== null,
    'Heeft machtiging van ander' => optional($user->proxy_for)->name ?? false,
    'Telraad' => $user->is_monitor,
    'Aanwezig' => $user->is_present,
];
@endphp

@section('content')
<h1 class="font-title font-normal text-2xl">
    Details voor <strong class="font-bold">{{ $user->name }}</strong>
</h1>

<dl class="flex flex-row flex-wrap items-center mb-8">
    @foreach ($facts as $label => $value)
    <dt class="flex-grow w-1/2">{{ $label }}</dt>
    <dd class="flex-none mr-4">
        {{ is_bool($value) ? ($value ? 'Ja' : 'Nee') : $value }}
    </dd>
    @endforeach
</dl>

<h2 class="font-title text-lg mb-2">Machtiging beheren</h2>

@if ($user->proxyFor !== null)
<div class="rounded shadow p-4 flex flex-row items-center">
    <div class="mr-2">Gemachtigd om te stemmen namens: </div>
    <div class="font-bold">
        <a href="{{ route('admin.users.show', ['user' => $user->proxyFor]) }}">
            {{ $user->proxyFor->name }}
        </a>
    </div>
</div>
@elseif ($user->proxy)
<form method="POST" action="{{ route('admin.users.proxy', compact('user')) }}" class="rounded shadow p-4">
    @csrf
    <div class="flex flex-row items-center">
    <div class="mr-2">Machtiging afgegeven aan: </div>
    <div class="font-bold">
        <a href="{{ route('admin.users.show', ['user' => $user->proxy]) }}">
            {{ $user->proxy->name }}
        </a>
    </div>
    </div>

    @can('setProxy', [$user, null])
    <input type="hidden" name="action" value="unset">
    <button class="btn btn--primary btn--narrow w-full text-center">Ontkoppelen</button>
    @endcan
</form>
@elseif (request()->user()->can('setProxy', [$user, null]))
<form method="POST" action="{{ route('admin.users.proxy', compact('user')) }}" class="rounded shadow p-4">
    @csrf
    <input type="hidden" name="action" value="set">
    <div class="flex flex-row items-center">
        <label for="grant" class="mr-2 flex-none">Machtiging afgegeven aan:</label>
        <select name="user_id" id="grant" class="w-1/2 flex-grow">
            @foreach ($proxies as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    <button class="btn btn--primary btn--narrow w-full text-center">Koppelen</button>
</form>
@elseif ($user->is_voter)
<div class="notice notice--warning">
    Je kan momenteel niet de machtigingen aanpassen.
</div>
@else
<div class="notice notice--warning">
    Deze gebruiker kan geen machtiging afgeven
</div>
@endif

<h2 class="font-title text-lg mb-2 mt-4">Instellen als telraad</h2>

<form method="POST" action="{{ route('admin.users.proxy', compact('user')) }}" class="rounded shadow px-4">
    @csrf
    <div class="flex flex-row items-center">
        <p class="mr-4">
            {{ $user->name }} is momenteel {{ $user->is_monitor ? 'wel' : 'geen' }} lid van de telraad
        </p>
        <button name="action" value="{{ $user->is_monitor ? 'unset' : 'set '}}" class="btn btn--brand btn--narrow">wisselen</button>
    </div>
</form>

<div class="my-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn--brand btn--wide">Terug naar overzicht</a>
</div>

@endsection
