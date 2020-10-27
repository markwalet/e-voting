@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Ledennbeheer
</h1>

<p class="text-lg">
    Hieronder staan de leden in het systeem. Je kan ook filteren:
</p>

<div class="flex flex-row items-center justify-stretch flex-wrap sm:flex-no-wrap">
    <a href="{{ route('admin.users.index') }}" class="btn btn--brand btn--narrow">Alle</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.users.index', ['only' => 'present']) }}" class="btn btn--brand btn--narrow">Aanwezig</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.users.index', ['only' => 'proxy']) }}" class="btn btn--brand btn--narrow">Machtiging uitgegeven</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.users.index', ['only' => 'is-proxy']) }}" class="btn btn--brand btn--narrow">Gemachtigd</a>
</div>

<table class="w-full">
    <thead>
        <tr class="bg-gray-50 border-b border-b-gray-300 text-left p-2">
            <th>Naam</th>
            <th>Stemrecht</th>
            <th>Aanwezig</th>
            <th>Machtiging</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td class="py-2"><a href="{{ route('admin.users.show', compact('user')) }}">{{ $user->name }}</a></td>
            <td class="py-2">{{ $user->vote_label }}</td>
            <td class="py-2">{{ $user->is_present ? 'Ja' : 'Nee' }}</td>
            <td class="py-2">{{ optional($user->proxy)->name ?? '–' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
