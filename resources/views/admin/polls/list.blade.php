@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Beheer <strong class="font-bold">stemmingen</strong>
</h1>

<p class="text-lg">
    Hieronder zie je de stemmingen die in dit systeem staan. Ook deze kan je filteren.
</p>

<div class="flex flex-row items-center justify-stretch flex-wrap sm:flex-no-wrap">
    <a href="{{ route('admin.polls.index') }}" class="btn btn--brand btn--narrow">Alle</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.polls.index', ['only' => 'concept']) }}" class="btn btn--brand btn--narrow">Concept</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.polls.index', ['only' => 'opened']) }}" class="btn btn--brand btn--narrow">Geopend</a>
    <div class="flex-none w-4"></div>
    <a href="{{ route('admin.polls.index', ['only' => 'closed']) }}" class="btn btn--brand btn--narrow">Gesloten</a>
</div>

{{-- Forms --}}
<form id="poll-action" action="" method="post">@csrf</form>

@each('admin.polls.poll', $polls, 'poll', 'admin.polls.empty')

<h2 class="font-title font-normal text-lg">
    stemming aanmaken
</h2>

<form action="{{ route('admin.polls.create') }}" method="post" class="my-4 flex flex-col items-stretch">
    @csrf
    {{-- Title --}}
    <div class="form-field">
        <label for="title" class="form-field__label">Vraag</label>
        <textarea class="form-field__input form-input" name="title" id="title">{{ old ('title')}}</textarea>
    </div>

    {{-- Submit --}}
    <div class="form-field">
        <button class="btn btn--brand">Aanmaken</button>
    </div>
</form>

@endsection
