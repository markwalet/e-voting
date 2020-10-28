<?php

declare(strict_types=1);

namespace App\Models;

class PollResults
{
    public int $favor;
    public int $against;
    public int $blank;
    public int $total;

    public function __construct(int $favor, int $against, int $blank)
    {
        $this->favor = $favor;
        $this->against = $against;
        $this->blank = $blank;
        $this->total = $favor + $against + $blank;
    }
}
