<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Poll;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
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
        if (Gate::forUser($user)->allows('admin')) {
            return true;
        }

        return Gate::forUser($user)->allows('view') && $poll->started_at !== null;
    }

    /**
     * Determine whether the user can view the model.
     * @param  \App\Models\User  $user
     * @param  \App\Models\Poll  $poll
     * @return mixed
     */
    public function vote(User $user, Poll $poll)
    {
        if (!Gate::forUser($user)->allows('vote')) {
            return false;
        }

        return $poll->is_open;
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
