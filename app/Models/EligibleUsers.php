<?php

declare(strict_types=1);

namespace App\Models;

class EligibleUsers
{
    public int $presentVoters;
    public int $presentProxies;
    public int $totalVotes;

    public function __construct(int $voters, int $proxies, ?int $totalVotes = null)
    {
        $this->presentVoters = $voters;
        $this->presentProxies = $proxies;
        $this->totalVotes = $totalVotes ?? ($voters + $proxies);

        \assert($this->totalVotes === ($this->presentProxies + $this->presentVoters));
    }
}
