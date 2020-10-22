<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ConscriboService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\OutputInterface;

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
        // Get people
        $this->line('Fetching users...', null, OutputInterface::VERBOSITY_VERBOSE);
        $people = $service->getResource('persoon');

        $totalCount = count($people);
        $this->info("Retrieved $totalCount user(s)");

        // Disable fill protections
        User::unguard();

        // Prep counts
        $parseCount = 0;
        $newCount = 0;

        // Get all users
        foreach ($people as $person) {
            // Parse user ID
            $userId = \filter_var($person['code'], \FILTER_VALIDATE_INT);

            // Throw a fit
            if ($userId === false) {
                $this->error(
                    "Skipping {$person['weergavenaam']}, ID not parseable",
                    OutputInterface::VERBOSITY_VERBOSE
                );
                continue;
            }

            // Require an e-mail address
            if (empty($person['email'])) {
                $this->error(
                    "Skipping {$person['weergavenaam']}, e-mailadres missing",
                    OutputInterface::VERBOSITY_VERBOSE
                );
                continue;
            }

            // Find the user
            $user = User::firstOrNew(['conscribo_id' => $userId]);

            // Build the name
            $user->name = implode(' ', array_filter([
                $person['voornaam'],
                $person['tussenvoegsel'],
                $person['naam'],
            ], static fn ($val) => !empty($val)));

            // Assign email and mark as verified
            $user->email = $person['email'];
            $user->email_verified_at = Date::now();

            // Add phone if set
            if (!empty($person['telefoonnummer'])) {
                $user->phone = (string) $person['telefoonnummer'];
            } else {
                $user->phone = null;
            }

            // Assign a random password if unset
            if (!$user->password) {
                $user->password = Hash::make(Str::uuid());
            }

            // Done
            $user->save();

            // Done
            $act = $user->wasRecentlyCreated ? "Created" : "Updated";
            $this->line(sprintf(
                "%s user <info>%s</> (<comment>%d</>)",
                $act,
                $user->name,
                $user->conscribo_id
            ), null, OutputInterface::VERBOSITY_VERBOSE);

            // Add counts
            $parseCount++;
            if ($user->wasRecentlyCreated) {
                $newCount++;
            }
        }

        // Re-enable protections
        User::reguard();

        // Report stats
        $this->line(sprintf(
            'Processed <info>%d</> of <info>%d</> users, (<info>%d</> were added)',
            $parseCount,
            $totalCount,
            $newCount
        ));

        return 0;
    }
}
