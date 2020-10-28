<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Poll;
use App\Models\PollApproval;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PollApprovalPolicy
{
    use HandlesAuthorization;

    /**
     * Check if creating a new poll approval is allowed
     * @param User $user
     * @param Poll $poll
     * @return bool
     */
    public function create(User $user, Poll $poll)
    {
        // Disallow if not the right rank
        if (!$user->can('monitor')) {
            return false;
        }

        // Disallow if already exists
        return !PollApproval::where([
            'user_id' => $user->id,
            'poll_id' => $poll->id
        ])->exists();
    }
}
