<div wire:poll.60s>
    {{-- Message that this updates --}}
    <div class="text-right text-sm text-gray-500 mb-4">De lijst updatet automatisch</div>
    @forelse ($this->polls as $index => $poll)
    <livewire:poll-vote-card :poll="$poll" :key="'poll-'.$poll->id" />
    @empty
    <div class="notice">
        Er zijn momenteel geen actieve voorstellen.
    </div>
    @endforelse
</div>
