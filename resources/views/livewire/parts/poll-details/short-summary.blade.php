@php
$results = $poll->results;
\assert($results instanceof \App\Models\ArchivedResults);
$votes = optional($results)->results;
$approval = optional($results)->approval;
$isApproved = $approval->positive === $approval->total;
@endphp
@if ($results)
<div class="w-full flex flex-col items-stretch md:flex-row md:items-center">
<div class="flex-none number-grid number-grid--small">
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $votes->favor }}</data>
        <small class="number-grid__label">Voor</small>
    </div>
    <div class="number-grid__separator">+</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $votes->against }}</data>
        <small class="number-grid__label">Tegen</small>
    </div>
    <div class="number-grid__separator">+</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $votes->blank }}</data>
        <small class="number-grid__label">Onthouding</small>
    </div>
    <div class="number-grid__separator">=</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $votes->total }}</data>
        <small class="number-grid__label">Totaal</small>
    </div>
</div>
<div class="flex-grow"></div>
<div class="flex-none number-grid number-grid--small">
    <div class="number-grid__tile">
        <data class="number-grid__number {{ $isApproved ? 'number-grid__number--green' : 'number-grid__number--red' }}">{{ $isApproved ? 'OK' : 'FAIL' }}</data>
        <small class="number-grid__label">Telraad</small>
    </div>
</div>
</div>
@else
<div class="notice notice--warning">
    Er zijn geen opgeslagen resultaten voor deze telling
</div>
@endif
