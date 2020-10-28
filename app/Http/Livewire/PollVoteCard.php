<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Poll;
use App\Models\PollVote;
use App\Models\User;
use App\Models\UserVote;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PollVoteCard extends Component
{
    use AuthorizesRequests;

    public Poll $poll;
    public string $vote = '';
    public string $message = '';
    public bool $expand = false;
    public bool $expandProxy = false;

    /**
     * Self user
     * @return User
     * @throws BindingResolutionException
     */
    public function getUserProperty(): User
    {
        return \request()->user();
    }

    /**
     * Proxy user
     * @return null|User
     * @throws BindingResolutionException
     */
    public function getProxyProperty(): ?User
    {
        return $this->getUserProperty()->proxyFor;
    }

    /**
     * Render
     * @return View|Factory
     */
    public function render()
    {
        return view('livewire.poll-vote-card', [
            'options' => PollVote::VALID_VOTES
        ]);
    }

    /**
     * Cast the self vote
     * @return void
     */
    public function castVote(string $type)
    {
        // Get target
        $target = $type === 'user' ? $this->user : $this->proxy;

        // Check
        if (!$target instanceof User) {
            throw new BadRequestHttpException('Could not cast vote');
        }

        // Prep args
        $args = [$this->poll];
        if ($type !== 'user') {
            $args[] = $target;
        }

        // Auth args
        $this->authorize('castVote', $args);

        // Verify vote
        if (!\array_key_exists($this->vote, PollVote::VALID_VOTES)) {
            throw new BadRequestHttpException('Invalid vote');
        }

        // Register vote action
        UserVote::create([
            'user_id' => $target->id,
            'poll_id' => $this->poll->id
        ]);

        // Apply vote
        PollVote::create([
            'poll_id' => $this->poll->id,
            'vote' => $this->vote
        ]);

        // Report
        $this->message = "Je stem is uitgebracht.";

        // Collapse
        $this->expand = false;
        $this->expandProxy = false;
    }
}
