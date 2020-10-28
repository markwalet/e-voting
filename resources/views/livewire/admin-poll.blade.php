@component('partials.poll-card', compact('poll'))
<div class="flex flex-row items-center">
    {{-- Delete --}}
    @can('delete', $poll)
    <button class="w-1/4 btn btn--link btn--narrow" wire:click.prevent="delete">Verwijderen</button>
    <div class="flex-none w-4"></div>
    @endcan

    {{-- Start --}}
    @can('open', $poll)
    <button class="w-1/4 btn btn--link btn--narrow" wire:click.prevent="open">Openen</button>
    <div class="flex-none w-4"></div>
    @endcan

    {{-- Stop --}}
    @can('close', $poll)
    <button class="w-1/4 btn btn--link btn--narrow" wire:click.prevent="close">Sluiten</button>
    <div class="flex-none w-4"></div>
    @elseif ($poll->started_at !== null && $poll->ended_at === null)
    <button class="w-1/4 btn btn--link btn--narrow" disabled>Sluiten</button>
    <div class="flex-none w-4"></div>
    @endcan

    {{-- View approval --}}
    @if (!$showApprove)
    @can ('submitComplete', $poll)
    <button class="w-1/4 btn btn--link btn--narrow" wire:click.prevent="$set('showApprove', true)">Afronden</button>
    @elsecan('complete', $poll)
    <button class="w-1/4 btn btn--link btn--narrow" disabled>Afronden</button>
    @endif
    @endif

    @if ($poll->completed_at)
    @include('livewire.parts.poll-details.short-summary')
    @endif
</div>

{{-- Approval section --}}
@if ($showApprove && Auth::user()->can('complete', $poll))
    {{-- Number of users --}}
    @include('livewire.parts.poll-details.count-users')

    {{-- Number of votes --}}
    @include('livewire.parts.poll-details.count-votes')

    {{-- Number of approvals --}}
    @include('livewire.parts.poll-details.count-approvals')

    {{-- Review --}}
    @include('livewire.parts.poll-details.action-confirm')
@endif
@endcomponent
