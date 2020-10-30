<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Poll;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

class CanDeployCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'vote:can-deploy';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Checks if a new version can be deployed, which can\'t when there\'s an open or unconfirmed vote';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle()
    {
        // Count open plls
        $count = Poll::query()
            ->whereNotNull('started_at')
            ->whereNUll('completed_at')
            ->count();

        // Allow if none
        if ($count === 0) {
            Cache::put('sys.in-deploy', true, Date::now()->addMinutes(5));
            $this->info('Deploy allowed: no active votes');

            return 0;
        }

        // Fail
        $this->alert("There are {$count} active votes. Deployment blocked");
        return 1;
    }
}
