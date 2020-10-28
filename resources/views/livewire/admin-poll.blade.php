@component('partials.poll-card', compact('poll'))
<div class="flex flex-row items-center">
    {{-- Delete --}}
    @can('delete', $poll)
    <button class="w-1/4 btn btn--link btn--narrow" wire:click.prevent="delete">Verwijderen</button>
    @else
    <button class="w-1/4 btn btn--link btn--narrow" disabled>Verwijderen</button>
    @endcan

    {{-- Space --}}
    <div class="flex-none w-4"></div>

    {{-- Start --}}
    @can('open', $poll)
    <button class="w-1/4 btn btn--link btn--narrow" wire:click.prevent="open">Openen</button>
    @else
    <button class="w-1/4 btn btn--link btn--narrow" disabled>Openen</button>
    @endcan

    {{-- Space --}}
    <div class="flex-none w-4"></div>

    {{-- Stop --}}
    @can('close', $poll)
    <button class="w-1/4 btn btn--link btn--narrow" wire:click.prevent="close">Sluiten</button>
    @else
    <button class="w-1/4 btn btn--link btn--narrow" disabled>Sluiten</button>
    @endcan
</div>
@endcomponent
