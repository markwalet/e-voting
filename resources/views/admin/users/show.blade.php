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

<h2 class="font-title text-lg">Machtiging beheren</h2>
<div class="my-4 p-4 border rounded border-blue-800 bg-blue-100">TODO</div>

<h2 class="font-title text-lg">Aanwezigheid beheren</h2>

<div class="my-4 p-4 border rounded border-blue-800 bg-blue-100">TODO</div>

<div class="my-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn--brand btn--wide">Terug naar overzicht</a>
</div>

@endsection
