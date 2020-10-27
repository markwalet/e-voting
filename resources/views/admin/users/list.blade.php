@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Ledennbeheer
</h1>

<p class="text-lg mb-4">
    Hieronder staan de leden in het systeem.
</p>

<p class="mb-4">
    {{ $present }} stemgerechtigden aanwezig + {{ $proxied }} via machtiging aanwezig: {{ $present + $proxied }} stemmen.
</p>


<div class="flex flex-row items-center justify-stretch flex-wrap sm:flex-no-wrap">
    <a href="{{ route('admin.users.index') }}" class="btn btn--brand btn--narrow">Stemgerechtigd (standaard)</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.users.index', ['filter' => 'all']) }}" class="btn btn--brand btn--narrow">Alle</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.users.index', ['filter' => 'present']) }}" class="btn btn--brand btn--narrow">Aanwezig</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.users.index', ['filter' => 'proxy']) }}" class="btn btn--brand btn--narrow">Machtiging uitgegeven</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.users.index', ['filter' => 'is-proxy']) }}" class="btn btn--brand btn--narrow">Gemachtigd</a>
</div>

<form action="#" method="post" id="user-action" name="user-action">@csrf</form>


<table class="w-full">
    <thead>
        <tr class="bg-gray-50 border-b border-b-gray-300 text-left p-2">
            <th>Naam</th>
            <th>Stemrecht</th>
            <th>Aanwezig</th>
            <th>Machtiging</th>
            <th>Gemachtigd door</th>
        </tr>
    </thead>
    <tbody>
        @each('partials.admin.user', $users, 'user', 'partials.admin.no-user')
    </tbody>
</table>

<div class="mt-4">
    {{ $users->links() }}
</div>

@endsection
