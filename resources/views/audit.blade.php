@extends('layouts.base')

@section('content')
<h1 class="font-title font-bold text-2xl">
    Installatie <span class="font-normal">controleren</span>
</h1>

<p class="text-lg mb-4">
    Hieronder staat informatie om de versie van de software te valideren zonder
    toegang tot de server.
</p>

<p class="mb-4">
    De huidige applicatie draait op versie <code>{{ $version }}</code>.
</p>
{{--
<h2 class="font-title font-bold text-2xl">Download applicatie</h2>
    <a href="{{ route('audit.download') }}" class="btn btn--narrow btn--primary">Download huidige applicatiecode</a>

<div class="small text-gray-600 mb-8">
    <p>
        Met bovenstaande knop download je een zip-bestand van de applicatie. Dit
        archief bevat geen persoonsgegevens of applicatiedata.
    </p>
    <p class="font-bold">
        Dit archief genereren kost tijd, daarom is er een rate-limit van 6 downloads per uur.
    </p>
    </p>
</div>
--}}
@endsection

@section('content-after')
<div class="container container--wide">
    <h2 class="font-title font-bold text-2xl">Status van de code</h2>
    <pre class="p-4 rounded bg-gray-800 text-white overflow-x-auto mb-4">{{ trim($status) }}</pre>

    <h2 class="font-title font-bold text-2xl">Code checksums</h2>
    <table class="w-full">
        <thead>
            <tr>
                <th>Bestand</th>
                <th>Checksum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sums as $file => $hash)
            <tr>
                <td><code>{{ $file }}</code></td>
                <td><code>{{ $hash }}</code></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
