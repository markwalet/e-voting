<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class PollApprovalResults implements JsonSerializable
{
    public int $positive;
    public int $negative;
    public int $neutral;
    public int $total;
    public array $approvals;

    public function __construct(int $positive, int $negative, int $neutral, iterable $approvals)
    {
        $this->positive = $positive;
        $this->negative = $negative;
        $this->neutral = $neutral;
        $this->total = $positive + $negative + $neutral;

        $this->setApprovals($approvals);
    }

    /**
     * Convert approvals to an array
     * @param iterable $approvals
     * @return void
     */
    private function setApprovals(iterable $approvals): void
    {
        $out = [];
        foreach ($approvals as $approve) {
            if ($approve instanceof PollApproval) {
                $out[] = [
                $approve->created_at->format('d-m-Y H:i:s (T)'),
                $approve->user->name,
                $approve->result_name
                ];
            } elseif (is_array($approve)) {
                $out[] = $approve;
            }
        }
        $this->approvals = $out;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'positive' => $this->positive,
            'negative' => $this->negative,
            'neutral' => $this->neutral,
            'total' => $this->total,
            'approvals' => $this->approvals
        ];
    }
}
