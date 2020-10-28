<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class AdminPoll extends Component
{
    use AuthorizesRequests;

    public Poll $poll;

    public function render()
    {
        return view('livewire.admin-poll');
    }
    /**
     * Opens the poll
     * @return void
     */
    public function open()
    {
        // Check
        $this->authorize('open', $this->poll);

        // Start the poll
        $this->poll->started_at = now();
        $this->poll->start_count = User::getEligibleUsers()->totalVotes;
        $this->poll->save();
    }

    /**
     * Closes the poll
     * @return void
     */
    public function close(): void
    {
        // Check
        $this->authorize('close', $this->poll);

        // Stop the poll
        $this->poll->ended_at = now();
        $this->poll->end_count = User::getEligibleUsers()->totalVotes;

        // Save the new poll
        $this->poll->save();
    }

    /**
     * Removes the poll
     * @return void
     */
    public function delete(): void
    {
        // Check
        $this->authorize('delete', $this->poll);

        // Remove it
        $this->poll->delete();
    }
}
