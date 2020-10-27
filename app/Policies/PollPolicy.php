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
     * Determine whether the user can view the model.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Poll  $poll
     * @return bool
     */
    public function vote(User $user, Poll $poll, ?User $proxy = null): bool
    {
        // Disallow if no vote right
        if (!$user->is_voter) {
            return false;
        }

        // Disallow if transferred, or if this is a transfer check
        // and it's not transferred
        if (
            ($user->proxy !== null && !$proxy) ||
            ($user->proxy === null && $proxy)
        ) {
            return false;
        }

        return $poll->is_open;
    }

    public function voteSelf(User $user, Poll $poll): bool
    {
        if (!$user->is_voter) {
            return false;
        }
    }

    /**
     * Determine whether the user can create models.
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return Gate::forUser($user)->allows('admin');
    }

    /**
     * Determine whether the user can update the model.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Poll  $poll
     * @return mixed
     */
    public function update(User $user, Poll $poll)
    {
        // Only allow admins
        if (!Gate::forUser($user)->allows('admin')) {
            return false;
        }

        // Only allow if not yet started
        return $poll->started_at === null;
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
        if (!Gate::forUser($user)->allows('admin')) {
            return false;
        }

        // Only allow if not yet started
        return $poll->started_at === null;
    }
}
