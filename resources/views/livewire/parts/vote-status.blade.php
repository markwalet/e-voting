<div class="flex flex-row w-full items-center">
    <p class="flex-grow mr-2">Jouw stem</p>
    <div class="w-64 text-right">
        @if ($this->user->can('vote', $poll) && $this->user->can('castVote', $poll))
        <button class="btn btn--brand btn--narrow my-0" wire:click="$set('expand', true)">Nu stemmen</button>
        @elseif ($this->user->can('vote', $poll) && !$this->user->can('castVote', $poll))
        <p class="text-brand-600 font-bold">Stem uitgebracht</p>
        @else
        <p class="text-yellow-600">Geen stemrecht</p>
        @endcan
    </div>
</div>

@if ($this->proxy)
<div class="flex flex-row w-full items-center mt-4">
    <p class="flex-grow mr-2">Machtiging van {{ $this->proxy->name }}</p>
    <div class="w-64 text-right">
        @if ($this->user->can('vote', [$poll, $this->proxy]) && $this->user->can('castVote', [$poll, $this->proxy]))
        <button class="btn btn--brand btn--narrow my-0" wire:click="$set('expandProxy', true)">Nu stemmen</button>
        @elseif ($this->user->can('vote', [$poll, $this->proxy]) && !$this->user->can('castVote', [$poll, $this->proxy]))
        <p class="text-brand-600 font-bold">Stem uitgebracht</p>
        @else
        <p class="text-yellow-600">Geen stemrecht</p>
        @endcan
    </div>
</div>
@endif
