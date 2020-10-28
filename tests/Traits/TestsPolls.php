<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;

trait TestsPolls
{
    /**
     * Makes a new poll
     * @return Poll
     */
    protected function createPoll(): Poll
    {
        return new Poll([
            'title' => sprintf(
                '[test] Random test poll at %s',
                Date::now()->toFormattedDateString()
            )
        ]);
    }

    /**
     * Starts the given poll with the given number of votes
     * @param Poll $poll
     * @param null|int $votes
     * @return void
     * @throws Exception
     */
    protected function startPoll(Poll $poll, ?int $votes = null): void
    {
        $poll->started_at = now();
        $poll->start_count = $votes ?? \random_int(5, 60);
    }

    /**
     * Ends the given poll
     * @param Poll $poll
     * @param null|int $votes
     * @return void
     * @throws Exception
     */
    protected function endPoll(Poll $poll, ?int $votes = null): void
    {
        $poll->ended_at = now();
        $poll->end_count = $votes ?? \random_int(5, 60);
    }

    /**
     * Creates a set of random votes
     * @param Poll $poll
     * @param int $votes
     * @return void
     */
    protected function createPollVotes(Poll $poll, int $votes): void
    {
        for ($i = 0; $i < $votes; $i++) {
            PollVote::create([
                'poll_id' => $poll->id,
                'vote' => Arr::random(array_keys(PollVote::VALID_VOTES))
            ]);
        }
    }
}
