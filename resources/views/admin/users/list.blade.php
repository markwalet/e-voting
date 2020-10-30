@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Ledennbeheer
</h1>

<p class="text-lg mb-4">
    Hieronder staan de leden in het systeem.
</p>

{{-- Members --}}
<livewire:admin-user-list />

{{-- Sep --}}
<hr class="border-gray-400 mt-4 mb-8" />

{{-- Update button --}}
<h3 class="font-title text-xl font-bold mb-4">Update leden</h3>

<p>Hieronder kan je de gegevens van de leden updaten. <strong>Deze actie duurt ~1 minuut</strong>. Klik slechts 1x!</p>

<form action="{{ route('admin.users.refresh') }}" method="post">
    @csrf
    <button type="submit" class="btn btn--brand btn--wide w-full">Update leden</button>
</form>

{{-- Mark absent --}}
<h3 class="font-title text-xl font-bold mb-4">Iedereen afmelden</h3>

<p>Met onderstaande knop markeer je iedereen weer als afwezig.</p>

<form action="{{ route('admin.users.reset') }}" method="post">
    @csrf
    <button type="submit" class="btn btn--brand btn--wide w-full">Kickhammer 3000</button>
</form>

@endsection
