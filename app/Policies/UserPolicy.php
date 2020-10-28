<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\ChecksIfVoteIsRunning;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
    use ChecksIfVoteIsRunning;

    /**
     * Create a new policy instance.
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Checks if $user can change the presence of a user. Allowed for admins outside
     * of voting windows
     * @param User $user
     * @return bool
     * @throws BindingResolutionException
     */
    public function setPresent(User $user)
    {
        // Reject if not admin or in mutation
        if (!$user->can('admin') || $this->hasRunningVote()) {
            return false;
        }

        return true;
    }

    /**
     * Checks if $user can set $proxy as a proxy of $target. $proxy can be null
     * to unset a proxy. Disallowed if $user is not an admin, if a vote is running,
     * if $target can't vote or if (when provided) $proxy can't be a proxy
     * @param User $user
     * @param User $target
     * @param null|User $proxy
     * @return bool
     */
    public function setProxy(User $user, User $target, ?User $proxy): bool
    {
        // Reject if not admin or in mutation
        if (!$user->can('admin') || $this->hasRunningVote()) {
            return false;
        }

        // The target needs to be able to vote to assign a proxy
        if (!$target->is_voter) {
            return false;
        }

        // Allow unsetting
        if ($proxy === null) {
            return true;
        }

        // The proxy needs to be able to be a proxy
        if (!$proxy->can_proxy) {
            return false;
        }

        // The proxy cannot already be a proxy
        if ($proxy->proxyFor !== null && !$proxy->proxyFor->is($user)) {
            return false;
        }

        // The proxy can be assigned
        return true;
    }

    /**
     * Indicates if a User can be marked as monitor (who checks the system)
     * @param User $user
     * @param User $target
     * @return bool
     */
    public function setMonitor(User $user, User $target)
    {
        // Reject if not admin
        if (!$user->can('admin')) {
            return false;
        }

        // Only allow users that can't vote, directly or via proxy
        return !$target->is_voter && !($target->can_proxy && $target->proxyFor);
    }
}
