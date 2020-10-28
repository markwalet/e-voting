@component('partials.poll-card', compact('poll'))
    {{-- Summary --}}
    <div class="flex flex-row items-center">
        {{-- Expand --}}
        <label class="w-full flex items-center flex-grow">
            <input type="checkbox" wire:model="expand" class="form-checkbox mr-4">
            Toon details
        </label>

        {{-- Given approval --}}
        @if ($approval)
        <div class="ml-4 text-brand-600">{{ $approval->result_name }}</div>
        @endif
    </div>

    {{-- Expand contents --}}
    @if ($expand)
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

    <h3 class="font-title text-xl mb-2 mt-4">Uitgebrachte stemmen</h3>

    <table class="w-full">
        <thead>
            <tr>
                <th>Datum en tijd</th>
                <th>Antwoord</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($poll->votes as $vote)
            <tr>
                <td>{{ $vote->created_at->format('d-m-Y H:i:s (T)') }}</td>
                <td>{{ App\Models\PollVote::VALID_VOTES[$vote->vote] ?? $vote->vote }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3 class="font-title text-xl mb-2 mt-4">Beoordeling</h3>

    @if ($approval)
        <p>Je hebt op {{ $approval->created_at->format('d-m-Y \o\m H:i:s') }} een "{{ $approval->result_name }}" beoordeling gegeven.</p>
    @elsecan('create', [App\Models\PollApproval::class, $poll])
        <p class="text-danger-600">
            LET OP: met de knoppen hieronder breng je een <strong>bindend</strong> oordeel uit over deze stemming.<br />
            Deze kan je hierna niet meer via dit systeem wijzigen.
        </p>

        <div class="flex flex-row items-center w-full">
        @foreach (App\Models\PollApproval::RESULTS as $name => $label)
            <button class="btn btn--brand btn--narrow w-1/4 flex-grow" wire:click="store('{{ $name }}')">
                {{ $label }}
            </button>
            @if (!$loop->last)
            <div class="w-4 flex-none"></div>
            @endif
        @endforeach
        </div>
    @else
        <p>Je kan momenteel geen oordeel uitbrengen op deze stemming.</p>
    @endif
    @endif
@endcomponent
