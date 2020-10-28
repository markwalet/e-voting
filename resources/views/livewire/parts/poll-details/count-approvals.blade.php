<h3 class="font-title text-xl mb-2 mt-4">Resultaten telraad</h3>
<div class="number-grid">
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $judgement->positive }}</data>
        <small class="number-grid__label">Goedgekeurd</small>
    </div>
    <div class="number-grid__separator">&nbsp;</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $judgement->negative }}</data>
        <small class="number-grid__label">Afgekeurd</small>
    </div>
    <div class="number-grid__separator">&nbsp;</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $judgement->neutral }}</data>
        <small class="number-grid__label">Geen uitspraak</small>
    </div>
</div>
