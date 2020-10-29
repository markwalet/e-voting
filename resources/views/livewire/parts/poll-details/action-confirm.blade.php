<h3 class="font-title text-xl mb-2 mt-4">Stemming afronden</h3>

@if ($poll->completed_at)
<p>Dit voorstel is op {{ $poll->completed_at->format('d-m-Y \o\m H:i:s (T)') }} afgerond.</p>
@elsecannot('complete', $poll)
<p>Je kan momenteel dit voorstel niet afronden.</p>
@else
<p class="text-danger-600">
    LET OP: met onderstaande knop vergrendel je de uitslagen. Het voorstel kan dan niet meer beoordeeld worden.
</p>

<div class="flex flex-row items-center w-full">
    <button class="btn btn--brand btn--narrow w-full" wire:click="confirm">
        Stemming afronden
    </button>
</div>
@endif
