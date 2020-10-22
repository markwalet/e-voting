<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ConscriboService;
use Illuminate\Console\Command;

class CreateUsersFromConscribo extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'vote:create-users';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Creates all users from Conscribo as users in this system';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(ConscriboService $service)
    {
        // Get all users
        foreach ($service->getResource('persoon') as $person) {
            $user = User::firstOrNew(['conscribo_id' => (int) $person['id']]);

            $user->name = implode(' ', array_filter([
                $person['voornaam'],
                $person['tussenvoegsel'],
                $person['naam'],
            ], static fn ($val) => !empty($val)));
            $user->email = $person['email'];
            $user->telefoonnummer = $person['telefoonnummer'];
        }
        return 0;
    }
}
