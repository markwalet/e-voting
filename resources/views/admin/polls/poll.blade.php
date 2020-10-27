<div class="rounded shadow mb-4 p-4">
    {{-- Title --}}
    <div class="flex flex-row items-start mb-4">
        <h3 class="font-title font-normal mr-4 w-full">{{ $poll->title }}</h3>
        <div class="text-brand-600 flex-none">
            @if ($poll->ended_at)
            Gesloten
            @elseif ($poll->started_at)
            Geopend
            @else
            Concept
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex flex-row items-center">
        <button name="poll" value="{{ $poll->id }}" class="btn btn--link btn--narrow" form="poll-action"
            formaction="{{ route('admin.polls.delete', compact('poll')) }}"
            {{ $poll->started_at !== null ? 'disabled' : '' }}>Verwijderen</button>
        <div class="flex-none w-4"></div>
        <button name="poll" value="{{ $poll->id }}" class="btn btn--brand btn--narrow" form="poll-action"
            formaction="{{ route('admin.polls.open', compact('poll')) }}"
            {{ $poll->started_at !== null ? 'disabled' : '' }}>Openen</button>
        <div class="flex-none w-4"></div>
        <button name="poll" value="{{ $poll->id }}" class="btn btn--brand btn--narrow" form="poll-action"
            formaction="{{ route('admin.polls.close', compact('poll')) }}"
            {{ $poll->ended_at !== null ? 'disabled' : '' }}>Sluiten</button>
    </div>
</div>
