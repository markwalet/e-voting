@component('partials.poll-card', ['poll' => $poll])

@if ($message)
<div class="notice notice--brand mb-4">{{ $message }}</div>
@endif

@if (!$this->user->can('vote', $poll) && (!$this->proxy || !$this->user->can('vote', [$poll, $this->proxy])))
{{-- Report not-available to vote --}}
<div class="notice notice--info">Je kan niet stemmen op dit voorstel</div>
@elseif ($this->user->can('castVote', $poll) && $this->expand)
{{-- Get self vote --}}
@include('livewire.parts.vote-buttons', ['type' => 'user'])
@elseif ($this->proxy && $this->user->can('castVote', [$poll, $this->proxy]) && $this->expandProxy)
{{-- Get other vote --}}
@include('livewire.parts.vote-buttons', ['type' => 'proxy'])
@else
@include('livewire.parts.vote-status')
@endif


@endcomponent
