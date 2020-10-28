@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Telling controle
</h1>

<p class="text-lg mb-4">
    Hieronder staan afgeronde tellingen in omgekeerd-chronologische volgorde (meest recent gesloten bovenaan)
</p>

@forelse ($polls as $poll)
<livewire:monitor-result :poll="$poll" :approval="$approvals->get($poll->id)" :key="$poll->id" />
@empty
<div class="notice notice--info">
    Er zijn geen stemmingen recentelijk gesloten
</div>
@endforelse

@endsection
