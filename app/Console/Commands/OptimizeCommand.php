<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Foundation\Console\OptimizeCommand as ConsoleOptimizeCommand;
use Illuminate\Support\Facades\Cache;

class OptimizeCommand extends ConsoleOptimizeCommand
{
    /**
     * Execute the console command.
     * @return int
     */
    public function handle()
    {
        $result = parent::handle() ?? 0;
        if ($result === 0) {
            Cache::forget('sys.in-deploy');
        }
        return $result;
    }
}
