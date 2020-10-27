<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ConscriboService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\OutputInterface;

class CreateUsersFromConscribo extends Command
{

    /**
     * Groups that allow an account
     */
    private const CREATE_USER_GROUPS = ['lid', 'erelid', 'a-leden', 'oud-lid', 'begunstigers'];

    /**
     * Groups that allow is_vote to be true
     */
    private const ALLOW_VOTING_GROUPS = ['lid', 'erelid'];
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'vote:create-users';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Creates users from Conscribo, that are likely to use this sytem. Updates `is_voter`';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(ConscriboService $service)
    {
        // Get roles
        $this->line('Fetching roles...', null, OutputInterface::VERBOSITY_VERBOSE);
        $roles = $this->getMappedGroupIds($service);
        $this->info("Retrieved {$roles->count()} role(s)");

        // Get people
        $this->line('Fetching users...', null, OutputInterface::VERBOSITY_VERBOSE);
        $people = collect($service->getResource('persoon'));
        $this->info("Retrieved {$people->count()} user(s)");

        // Disable fill protections
        $this->createOrUpdateUsersWithRoles($people, $roles);

        // Assign admin roles
        $this->call('vote:assign-permissions', ['--admin']);

        // Done
        return 0;
    }

    /**
     * Maps all groups to a list of IDs, to allow quick role assignment
     * @param ConscriboService $service
     * @return Collection<array<int>>
     */
    public function getMappedGroupIds(ConscriboService $service): Collection
    {
        // Get groups
        $groups = $service->getResourceGroups('persoon');

        return collect($groups)
            ->mapWithKeys(static fn ($group) => [Str::slug($group['name']) => $group['members']]);
    }

    /**
     * Update or create users
     * @param Collection<array> $users
     * @param Collection<array<int>> $roles
     * @return void
     */
    public function createOrUpdateUsersWithRoles(Collection $users, Collection $roles): void
    {

        User::unguard();

        // Prep counts
        $parseCount = 0;
        $newCount = 0;
        $totalCount = $users->count();

        /**
         * Checks if a user ID is present in the list of groups
         * @return bool
         */
        $inGroup = static function (int $userId, array $groups) use ($roles) {
            foreach ($groups as $wanted) {
                if (isset($roles[$wanted]) && \in_array($userId, $roles[$wanted])) {
                    return true;
                }
            }
            return false;
        };

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
            if (!$inGroup($userId, self::CREATE_USER_GROUPS)) {
                $this->comment("Ignoring {$userName}, no rights");
                continue;
            }

            // Find the user
            $user = User::firstOrNew(['conscribo_id' => $userId]);

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
            $user->is_voter = $inGroup($userId, self::ALLOW_VOTING_GROUPS);
            $judge = $user->is_voter ? 'allowed' : 'denied';
            $this->line("User <info>$userName</> vote judgement: <comment>$judge</>.", null);

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
