<div wire:poll.60s></div>
@cannot('vote')
<div class="notice notice--warning">
    <strong class="notice__title">Je mag niet stemmen</strong>
    @if (!$user->is_voter)
    Je hebt geen stemrecht op deze ALV.
    @elseif ($user->is_voter && !$user->is_present)
    Je bent niet aangemeld, meld je eerst aan bij het bestuur.
    @elseif ($user->proxy !== null)
    Je hebt {{ $user->proxy->name }} gemachtigd. Meld je bij het bestuur
    om de machtiging in te trekken.
    @else
    We weten niet waarom
    @endif
</div>
@endcan
