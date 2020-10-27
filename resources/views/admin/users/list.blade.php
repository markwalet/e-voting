@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Ledennbeheer
</h1>

<p class="text-lg">
    Hieronder staan de leden in het systeem. Je kan ook filteren:
</p>

<div class="flex flex-row items-center justify-stretch">
    <a href="{{ route('admin.users.index', ['only' => 'present']) }}" class="btn btn--brand">Aanwezig</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.users.index', ['only' => 'proxy']) }}" class="btn btn--brand">Machtiging uitgegeven</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.users.index', ['only' => 'is-proxy']) }}" class="btn btn--brand">Gemachtigd</a>
</div>

<table>
    <thead>
        <tr>
            <th>Naam</th>
            <th>Stemrecht</th>
            <th>Aanwezig</th>
            <th>Machtiging</th>
            <th>Acties</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->vote_label }}</td>
            <td>{{ $user->is_present ? 'Ja' : 'Nee' }}</td>
            <td>{{ optional($user->proxy)->name ?? '–' }}</td>
            <td><a href="{{ route('admin.users.show', compact('user')) }}">Bekijk</a>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
