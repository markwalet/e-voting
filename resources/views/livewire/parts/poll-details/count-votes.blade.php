<h3 class="font-title text-xl mb-2 mt-4">Totaalresultaten</h3>
<div class="number-grid">
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $results->favor }}</data>
        <small class="number-grid__label">Voor</small>
    </div>
    <div class="number-grid__separator">+</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $results->against }}</data>
        <small class="number-grid__label">Tegen</small>
    </div>
    <div class="number-grid__separator">+</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $results->blank }}</data>
        <small class="number-grid__label">Onthouding</small>
    </div>
    <div class="number-grid__separator">=</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $results->total }}</data>
        <small class="number-grid__label">totaal stemmen</small>
    </div>
</div>
