<?php

declare(strict_types=1);

namespace App\Models;

class PollResults
{
    public int $favor;
    public int $against;
    public int $blank;
    public int $total;
    public array $votes;

    public function __construct(int $favor, int $against, int $blank, iterable $votes)
    {
        $this->favor = $favor;
        $this->against = $against;
        $this->blank = $blank;
        $this->total = $favor + $against + $blank;

        $this->setVotes($votes);
    }
    /**
     * Convert approvals to an array
     * @param iterable $votes
     * @return void
     */
    private function setVotes(iterable $votes): void
    {
        $out = [];
        foreach ($votes as $vote) {
            if ($vote instanceof PollVote) {
                $out[] = [
                    $vote->created_at->format('d-m-Y H:i:s (T)'),
                    $vote->vote_label
                ];
            } elseif (is_array($vote)) {
                $out[] = $vote;
            }
        }
        $this->votes = $out;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'favor' => $this->favor,
            'against' => $this->against,
            'blank' => $this->blank,
            'total' => $this->total,
            'votes' => $this->votes
        ];
    }
}
