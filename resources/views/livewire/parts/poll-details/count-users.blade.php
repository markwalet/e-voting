<h3 class="font-title text-xl mb-2 mt-4">Stemgerechtigden</h3>
<div class="number-grid">
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $poll->start_count }}</data>
        <small class="number-grid__label">Bij aanvang</small>
    </div>
    <div class="number-grid__separator">&nbsp;</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $poll->end_count }}</data>
        <small class="number-grid__label">Bij sluiting</small>
    </div>
    <div class="number-grid__separator">&nbsp;</div>
    <div class="number-grid__tile">
        <data class="number-grid__number">{{ $poll->start_count - $poll->end_count }}</data>
        <small class="number-grid__label">Verschil</small>
    </div>
</div>
