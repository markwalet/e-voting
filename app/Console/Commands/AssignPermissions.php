<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ConscriboService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AssignPermissions extends Command
{
    private const VOTE_GROUPS = ['lid', 'erelid'];

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = <<<'CMD'
    vote:assign-permissions
        {--admin : Update admin role}
        {--vote : Update vote role}
    CMD;

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Assign vote and admin permissions';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(ConscriboService $service)
    {
        // Wrap in a transaction
        DB::beginTransaction();

        // First off, reset all users
        User::query()->update([
            'is_voter' => false,
            'is_admin' => false
        ]);

        // Get vote groups
        $groups = $service->getResourceGroups('persoon');
        $voteGroups = Arr::only($groups, self::VOTE_GROUPS);
        foreach ($voteGroups as $group) {
            // Start
            $this->line("Applying <comment>vote</> permissions for <info>{$group['name']}</>");

            // Simply match by conscribo ID
            User::whereIn('conscribo_id', $group['members'])->update(['is_voter' => true]);

            // Report
            $this->info('Users updated');
        }

        // Get board
        $board = $service->getResource('commissie', ['code' => 1337]);
        foreach ($board as $committee) {
            if (empty($committee['members'])) {
                continue;
            }

            // Start
            $this->line("Applying <comment>admin</> permissions for <info>{$committee['naam']}</>");

            // Simply match by conscribo ID
            User::whereIn('conscribo_id', $committee['members'])->update(['is_admin' => true]);

            // Report
            $this->info('Users updated');
        }

        DB::commit();
        return 0;
    }
}
