<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\User;

trait TestsUsers
{
    /**
     * Makes a new poll
     * @return Poll
     */
    protected function createUser(
        bool $canVote = false,
        bool $canProxy = false,
        bool $canAdmin = false,
        bool $canMonitor = false,
        bool $isPresent = false
    ): User {
        return User::factory()->createOne([
            'is_voter' => $canVote,
            'is_admin' => $canProxy,
            'is_monitor' => $canAdmin,
            'can_proxy' => $canMonitor,
            'is_present' => $isPresent
        ]);
    }

    /**
     * Sets the proxy for a given user
     * @param User $user
     * @param null|User $proxy
     * @return void
     */
    protected function assignProxy(User $user, ?User $proxy): void
    {
        if ($proxy) {
            // Set proxy
            $proxy->proxy()->associate($user);
            $proxy->save();
        } elseif ($user->proxyFor) {
            // unset if set
            $proxy = $user->proxyFor;
            $proxy->proxy()->dissociate();
            $proxy->save();
        }

        // Refresh user
        $user->unsetRelation('proxyFor')->load('proxyFor');
    }

    /**
     * Flags user as present
     * @param User $user
     * @param bool $present
     * @return void
     * @throws InvalidArgumentException
     */
    protected function setPresent(User $user, bool $present = true): void
    {
        $user->is_present = $present;
        $user->save();
    }

    /**
     * Flags user as present
     * @param User $user
     * @param bool $present
     * @return void
     * @throws InvalidArgumentException
     */
    protected function setAdmin(User $user, bool $admin = true): void
    {
        $user->is_admin = $admin;
        $user->save();
    }

    /**
     * Flags user as present
     * @param User $user
     * @param bool $present
     * @return void
     * @throws InvalidArgumentException
     */
    protected function setMonitor(User $user, bool $monitor = true): void
    {
        $user->is_monitor = $monitor;
        $user->save();
    }

    /**
     * Flags user as present
     * @param User $user
     * @param bool $voter
     * @return void
     * @throws InvalidArgumentException
     */
    protected function setVoter(User $user, bool $voter = true): void
    {
        $user->is_voter = $voter;
        $user->save();
    }

    /**
     * Flags user as present
     * @param User $user
     * @param bool $canProxy
     * @return void
     * @throws InvalidArgumentException
     */
    protected function setCanProxy(User $user, bool $canProxy = true): void
    {
        $user->can_proxy = $canProxy;
        $user->save();
    }
}
