@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Beheer <strong class="font-bold">stemmingen</strong>
</h1>

<p class="text-lg">
    Hieronder zie je de stemmingen die in dit systeem staan. Ook deze kan je filteren.
</p>

<livewire:admin-poll-list />

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
