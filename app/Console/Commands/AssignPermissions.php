<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\SetsUserRights;
use App\Models\User;
use App\Services\ConscriboService;
use Illuminate\Support\Facades\DB;

class AssignPermissions extends ProductionCommand
{
    use SetsUserRights;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'vote:assign-permissions';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Assigns vote and admin permissions for existing users';

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
            'is_admin' => false,
            'can_proxy' => false
        ]);

        // Get all users
        User::query()->chunk(25, function ($users) use ($service) {
            foreach ($users as $user) {
                $this->setUserRights($service, $user);
                $user->save();
            }
        });

        // Apply changes
        DB::commit();
        return 0;
    }
}
