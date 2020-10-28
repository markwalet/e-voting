<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Traits\SetsUserRights;
use App\Models\User;
use App\Services\ConscriboService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUsersFromConscribo extends ProductionCommand
{
    use SetsUserRights;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = <<<'CMD'
    vote:create-users
        { --prune : Remove users not in Conscribo }
        { --update : Don't add new users, only update }
    CMD;

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Pulls data from Conscribo, optionally only updates or prunes missing.';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(ConscriboService $service)
    {
        // Get people
        $this->line('Fetching users...', null, OutputInterface::VERBOSITY_VERBOSE);
        $people = collect($service->getResource('persoon'));
        $this->info("Retrieved {$people->count()} user(s)");

        // Create and update according to data
        $this->createOrUpdateUsersWithRoles(
            $service,
            $people,
            ((bool) $this->option('update')) === false
        );

        // Remove excess if --prune
        if ($this->option('prune')) {
            // Get IDs
            $userIds = collect($people)
                ->filter(static fn ($row) => !empty($row['code']))
                ->map(static fn ($row) => \filter_var($row['code'], \FILTER_VALIDATE_INT))
                ->values()
                ->all();

            // Find users that don't match
            $users = User::whereNotIn('conscribo_id', $userIds)
                ->get(['id', 'name']);

            // Drop users
            foreach ($users as $user) {
                \assert($user instanceof User);

                // Delete vote records
                $user->votes()->delete();

                // Delete vote approvals
                $user->pollApprovals()->delete();

                // Check for a proxy
                if ($user->proxyFor) {
                    $proxy = $user->proxyFor;
                    $proxy->proxy()->dissociate();
                    $proxy->save();
                }

                // Delete user
                $user->delete();

                // Log
                $this->line("Pruned <info>{$user->name}</>");
            }
        }

        // Done
        return 0;
    }

    /**
     * Update or create users
     * @param Collection<array> $users
     * @param Collection<array<int>> $roles
     * @param bool $allowCreation
     * @return void
     */
    public function createOrUpdateUsersWithRoles(
        ConscriboService $service,
        Collection $users,
        bool $allowCreation = true
    ): void {
        User::unguard();

        // Prep counts
        $parseCount = 0;
        $newCount = 0;
        $totalCount = $users->count();

        // Get all users
        foreach ($users as $userData) {
            // Parse user ID
            $userId = \filter_var($userData['code'], \FILTER_VALIDATE_INT);
            $userName = $userData['weergavenaam'];

            // Throw a fit
            if ($userId === false) {
                $this->error(
                    "Skipping {$userName}, ID not parseable",
                    OutputInterface::VERBOSITY_VERBOSE
                );
                continue;
            }

            // Require an e-mail address
            if (empty($userData['email'])) {
                $this->error(
                    "Skipping {$userName}, e-mailadres missing",
                    OutputInterface::VERBOSITY_VERBOSE
                );
                continue;
            }

            // Check if user should be created
            if (!$this->shouldProcessUser($service, $userId)) {
                $this->comment("Ignoring {$userName}, no rights");
                continue;
            }

            // Find the user
            $user = User::firstOrNew(['conscribo_id' => $userId]);

            if (!$allowCreation && !$user->exists) {
                $this->comment("Ignoring {$userName}, only updating");
                continue;
            }

            // Build the name
            $user->name = implode(' ', array_filter([
                $userData['voornaam'],
                $userData['tussenvoegsel'],
                $userData['naam'],
            ], static fn ($val) => !empty($val)));

            // Assign email and mark as verified
            $user->email = $userData['email'];
            $user->email_verified_at = Date::now();

            // Update roles
            $this->setUserRights($service, $user);

            // Add phone if set
            if (!empty($userData['telefoonnummer'])) {
                $user->phone = (string) $userData['telefoonnummer'];
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
    }
}
