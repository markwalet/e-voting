<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\ArchivedResults;
use App\Models\Poll;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

class AdminPoll extends Component
{
    use AuthorizesRequests;

    public Poll $poll;
    public bool $showApprove = false;

    public function render()
    {
        $data = [];
        if ($this->showApprove) {
            $data['results'] = $this->poll->calculateResults();
            $data['judgement'] = $this->poll->calculateApproval();
        }
        return view('livewire.admin-poll', $data);
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

    public function confirm(): void
    {
        // Get shorthand
        $poll = $this->poll;
        // Check
        $this->authorize('submitComplete', $poll);

        // Stop the poll
        $poll->completed_at = now();
        $poll->results = ArchivedResults::create($poll);

        // Save the new poll
        $poll->save();

        // Collapse
        $this->showApprove = false;
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
