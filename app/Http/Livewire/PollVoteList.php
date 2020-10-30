<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Poll;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * The list of active polls, for async reloading
 */
class PollVoteList extends Component
{
    /**
     * Returns the active polls
     * @return Collection
     */
    public function getPollsProperty(): Collection
    {
        // Get polls
        return Poll::query()
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->orderByDesc('started_at')
            ->get();
    }

    /**
     * Render the view
     * @return View|Factory|RedirectResponse
     */
    public function render()
    {
        // Redirect if guest
        if (Auth::guest()) {
            return \response()->redirectGuest(\route('login'));
        }

        // Get view
        return view('livewire.poll-vote-list');
    }
}
