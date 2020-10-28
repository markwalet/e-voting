<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class ArchivedResults implements JsonSerializable
{
    /**
     * Creates the results from the poll
     * @param Poll $poll
     * @return null|ArchivedResults
     */
    public static function create(Poll $poll): ?self
    {
        // Quick check
        if (
            $poll->started_at === null ||
            $poll->ended_at === null ||
            $poll->completed_at === null
        ) {
            return null;
        }

        // Compute values
        $results = $poll->calculateResults();
        $approval = $poll->calculateApproval();

        // Both values are required
        if (empty($results) || empty($approval)) {
            return null;
        }

        // Make model
        return new self(
            $poll->start_count,
            $poll->end_count,
            $results,
            $approval
        );
    }

    public int $startVotes;
    public int $endVotes;
    public PollResults $results;
    public PollApprovalResults $approval;

    public function __construct(
        int $startVotes,
        int $endVotes,
        PollResults $results,
        PollApprovalResults $approval
    ) {
        $this->startVotes = $startVotes;
        $this->endVotes = $endVotes;
        $this->results = $results;
        $this->approval = $approval;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'votes' => [
                'start' => $this->startVotes,
                'end' => $this->endVotes,
            ],
            'results' => $this->results,
            'approval' => $this->approval,
        ];
    }
}
