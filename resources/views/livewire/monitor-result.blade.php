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
    {{-- Weird warning --}}
    @includeWhen($poll->is_weird, 'livewire.parts.poll-details.warning-weird')

    {{-- Number of users --}}
    @include('livewire.parts.poll-details.count-users')

    {{-- Number of votes --}}
    @include('livewire.parts.poll-details.count-votes')

    {{-- Actual votes, timestamped --}}
    @include('livewire.parts.poll-details.cast-votes')

    {{-- Review --}}
    @include('livewire.parts.poll-details.action-approve')
    @endif
@endcomponent
