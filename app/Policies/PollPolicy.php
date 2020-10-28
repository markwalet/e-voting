<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Gate;

class PollPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return Gate::forUser($user)->allows('view');
    }

    /**
     * Determine whether the user can view the model.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Poll  $poll
     * @return mixed
     */
    public function view(User $user, Poll $poll)
    {
        // Admins see all
        if (Gate::forUser($user)->allows('admin')) {
            return true;
        }

        // Check if started
        $isStarted = $poll->started_at !== null && $poll->started_at > Date::now();
        $isNotEnded = $poll->ended_at === null;
        $isRecentlyEnded = $poll->ended_at > Date::now()->subDay();

        // Allow seeing it if active or recently active
        return $isStarted && ($isNotEnded || $isRecentlyEnded);
    }

    /**
     * Determine whether the user can, or could, vote for this poll
     * @param User $user
     * @param Poll $poll
     * @param null|User $proxy User to vote for
     * @return bool
     */
    public function vote(User $user, Poll $poll, ?User $proxy = null): bool
    {
        // Only if open
        if (!$poll->is_open) {
            return false;
        }

        // Only if user is marked as present
        if (!$user->is_present) {
            return false;
        }

        // Handle proxy
        if ($proxy) {
            return $this->voteProxy($user, $proxy);
        }

        // Handle normal
        return $this->voteSelf($user);
    }

    /**
     * Returns true if $user can vote for itself
     * @param User $user
     * @param Poll $poll
     * @return bool
     */
    private function voteSelf(User $user): bool
    {
        // Only if voting is allowed
        if (!$user->is_voter) {
            return false;
        }

        // Reject if user is proxied
        if ($user->proxy_id !== null) {
            return false;
        }

        // Only if not yet voted
        return true;
    }

    /**
     * Returns true if $user can vote for an external user $proxy as it's proxy
     * @param User $user
     * @param Poll $poll
     * @return bool
     */
    private function voteProxy(User $user, User $proxy): bool
    {
        // Only if the user can proxy and is the proxy
        if (!$user->can_proxy || !$user->proxyFor->is($proxy)) {
            dump('user');
            return false;
        }

        // Only if proxy can vote
        if (!$proxy->is_voter) {
            return false;
        }

        // Only if not yet voted
        return true;
    }

    /**
     * Returns true if the user can still cast a vote
     * @param User $user
     * @param Poll $poll
     * @param null|User $proxy
     * @return bool
     */
    public function castVote(User $user, Poll $poll, ?User $proxy = null): bool
    {
        if (!$this->vote($user, $poll, $proxy)) {
            dump('Pre-check failed');
            return false;
        }

        $model = $proxy ? $proxy : $user;
        return !$model->votes()->where('poll_id', $poll->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('admin');
    }

    /**
     * Determine whether the user can open the poll
     * @param User $user
     * @param Poll $poll
     * @return mixed
     */
    public function open(User $user, Poll $poll)
    {
        // Only allow admins
        if (!$user->can('admin')) {
            return false;
        }

        // Only allow if not yet started
        return $poll->started_at === null;
    }

    /**
     * Determine whether the user can open the poll
     * @param User $user
     * @param Poll $poll
     * @return mixed
     */
    public function close(User $user, Poll $poll)
    {
        // Only allow admins
        if (!$user->can('admin')) {
            return false;
        }

        // Only allow if not yet stopped but running for a bit
        return $poll->ended_at === null
            && $poll->started_at !== null
            && $poll->started_at <= Date::now()->subSeconds(15);
    }

    /**
     * Determine whether the user can delete the model.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Poll  $poll
     * @return mixed
     */
    public function delete(User $user, Poll $poll)
    {
        // Only allow admins
        if (!$user->can('admin')) {
            return false;
        }

        // Only allow if not yet started
        return $poll->started_at === null && $poll->ended_at === null;
    }
}
