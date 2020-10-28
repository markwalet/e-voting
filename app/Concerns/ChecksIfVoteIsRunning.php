<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Poll;

trait ChecksIfVoteIsRunning
{
    /**
     * Returns true if a vote is running
     * @return bool
     * @throws InvalidArgumentException
     */
    protected function hasRunningVote(): bool
    {
        static $runningVote = null;
        $runningVote ??= Poll::query()
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->exists();
        return $runningVote;
    }
}
