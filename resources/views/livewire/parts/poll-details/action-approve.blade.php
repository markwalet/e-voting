<h3 class="font-title text-xl mb-2 mt-4">Oordeel</h3>

@if ($approval)
<p>Je hebt op {{ $approval->created_at->format('d-m-Y \o\m H:i:s (T)') }} een "{{ $approval->result_name }}" beoordeling
    gegeven.</p>
@elsecan('create', [App\Models\PollApproval::class, $poll])
<p class="text-danger-600">
    Via onderstaande knoppen breng je een digitaal oordeel uit over de resultaten van dit voorstel. Dit is enkel een digitale vastlegging. Je
    moet zelf, als telcommissie, dit communiceren naar de vergadering
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
<p>Je kan momenteel geen oordeel uitbrengen op dit voorstel.</p>
@endif
