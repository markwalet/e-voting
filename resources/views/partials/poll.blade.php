@php
$options = [
    'favor' => 'Voor',
    'against' => 'Tegen',
    'blank' => 'Onthouding'
];
$user = request()->user();
$proxy = $user->proxyFor;
$voteUsers = [
    $user->can('vote', $poll)
]
@endphp
<form class="rounded shadow mb-4 p-4">
    {{-- Title --}}
    <div class="flex flex-row items-start mb-4">
        <h3 class="font-title font-normal mr-4 w-full text-xl">{{ $poll->title }}</h3>
        <div class="text-brand-600 flex-none">{{ $poll->status }}</div>
    </div>

    {{-- Actions --}}
    @can('vote')
    @include('partials.poll.vote-self', compact('user', 'poll', 'proxy'))
    @includeWhen($proxy === null, 'partials.poll.vote-proxy', compact('user', 'poll', 'proxy'))
    @endcan
    <p class="text-lg">Voor <strong>Roelof Roos</strong> (jezelf)</p>
    <div class="poll-options">
        @foreach ($options as $value => $label)
        <label for="{{ $poll->id }}-{{ $value }}" class="poll-options__option">
            <input id="{{ $poll->id }}-{{ $value }}" type="radio" value="{{ $value }}" name="vote" class="form-radio poll-options__option-radio" />
            <span class="poll-options__option-label">{{ $label }}</span>
        </label>
        @endforeach
    </div>

    <p class="text-lg mt-4">Voor <strong>Tim van der Laan</strong></p>
    <div class="poll-options">
        @foreach ($options as $value => $label)
        <label for="{{ $poll->id }}-{{ $value }}-proxy" class="poll-options__option">
            <input id="{{ $poll->id }}-{{ $value }}-proxy" type="radio" value="{{ $value }}" name="vote-proxyy" class="form-radio poll-options__option-radio" />
            <span class="poll-options__option-label">{{ $label }}</span>
        </label>
        @endforeach
    </div>

    <div class="mt-8">
        <button class="btn btn--brand btn--wide w-full text-center">Stem uitbrengen</button>
    </div>
</form>
