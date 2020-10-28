<div>
    {{-- Render search --}}
    <div class="mb-4 flex flex-row items-center w-full">
        <label for="member-search" class="block mr-2 flex-none w-24">Zoeken</label>
        <input wire:model="search" id="member-search" type="search" autocomplete="off" autofocus
            class="form-input flex-grow" placeholder="Doorzoeken" />
    </div>

    {{-- Render fitlers --}}
    <div class="mb-4 flex flex-row items-center w-full">
        <label for="search-filter" class="block mr-2 flex-none w-24">Filter</label>
        <select class="form-select flex-grow" id="search-filter" wire:model="filter">
            <option value="recent">Afgelopen 24 uur bewerkt</option>
            <option value="complete">Alleen afgerond</option>
            <option value="closed">Allen gesloten</option>
            <option value="open">Alleen open</option>
            <option value="concepts">Alleen concepten</option>
            <option value="all">Niet filteren</option>
        </select>
    </div>

    {{-- Render users --}}
    <div class="mb-4">
        @forelse ($this->polls as $poll)
        <livewire:admin-poll :poll="$poll" :key="$poll->id" />
        @empty
        <div class="notice notice--info">
            Er zijn geen leden die voldoen aan de zoekresultaten
        </div>
        @endforelse
    </div>
</div>
