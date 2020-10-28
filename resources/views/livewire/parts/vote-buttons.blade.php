<form wire:submit.prevent="castVote('{{ $type }}')">
    <div class="poll-options mb-4">
        @foreach (\App\Models\PollVote::VALID_VOTES as $value => $label)
        <label class="poll-options__option">
            <input type="radio" value="{{ $value }}" name="vote" wire:model="vote"
                class="form-radio poll-options__option-radio" />
            <span class="poll-options__option-label">{{ $label }}</span>
        </label>
        @endforeach
    </div>
    @if ($vote)
    <div class="mt-8">
        <button type="submit" class="btn btn--brand btn--wide w-full text-center">Stem uitbrengen</button>
    </div>
    @endif
</form>
