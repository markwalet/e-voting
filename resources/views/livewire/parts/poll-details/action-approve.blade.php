<h3 class="font-title text-xl mb-2 mt-4">Tijden</h3>

@if ($approval)
<p>Je hebt op {{ $approval->created_at->format('d-m-Y \o\m H:i:s (T)') }} een "{{ $approval->result_name }}" beoordeling
    gegeven.</p>
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
