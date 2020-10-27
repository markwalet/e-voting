<?php

declare(strict_types=1);

namespace App\Console\Commands\Traits;

use App\Models\User;
use App\Services\ConscriboService;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Output\OutputInterface;

trait SetsUserRights
{
    /**
     * Groups that might show up for the meeting
     * @var array
     */
    private static array $accountGroups = ['lid', 'erelid', 'a-leden', 'oud-lid', 'begunstiger'];

    /**
     * Groups that are allowed to vote
     * @var array
     */
    private static array $voteGroups = ['lid', 'erelid'];

    /**
     * Groups that are allowed to vote
     * @var array
     */
    private static array $proxyGroups = ['lid', 'erelid', 'begunstiger'];

    /**
     * Codes of the group of which the members are admin
     * @var string
     */
    private static string $adminGroup = '1337';

    private array $userRightsCache = [];

    /**
     * Converts iterable results to name => member IDs
     * @param iterable $results
     * @return Collection
     */
    private function mapToMembers(iterable $results): Collection
    {
        return collect($results)
            ->mapWithKeys(static fn ($group) => [Str::slug($group['name'] ?? $group['naam']) => $group['members']]);
    }

    /**
     * Maps all groups to a list of IDs, to allow quick role assignment
     * @param ConscriboService $service
     * @return Collection<array<int>>
     */
    private function getGroupMembers(ConscriboService $service): Collection
    {
        // Get groups
        $this->userRightsCache['groups'] ??= $this->mapToMembers(
            $service->getResourceGroups('persoon')
        );

        // Return
        return $this->userRightsCache['groups'];
    }

    /**
     * Returns groups that are admins
     * @param ConscriboService $service
     * @return Collection
     */
    private function getAdminMembers(ConscriboService $service): Collection
    {
        // Get commissie
        $this->userRightsCache['admin'] ??= $this->mapToMembers(
            $service->getResource('commissie', ['code' => self::$adminGroup])
        )->collapse();

        // Return
        return $this->userRightsCache['admin'];
    }

    /**
     * Returns if the ID is in the collection somewhere
     * @param int $id
     * @param Collection $collection
     * @param array $keys
     * @return bool
     */
    private function IdInCollection(int $id, Collection $collection, array $keys): bool
    {
        return $collection->only($keys)->collapse()->contains($id);
    }

    /**
     * Returns if the given ID should be created
     * @param ConscriboService $service
     * @param int $id
     * @return bool
     */
    protected function shouldCreateUser(ConscriboService $service, int $id): bool
    {
        // Get groups
        $groups = $this->getGroupMembers($service);
        $admins = $this->getAdminMembers($service);

        // Check if in account group or an admin
        return $this->IdInCollection($id, $groups, self::$accountGroups)
            || $admins->contains($id);
    }

    /**
     * Updates the given user to match the data known in the service
     * @param ConscriboService $service
     * @param User $user
     * @return void
     * @throws InvalidArgumentException
     */
    public function setUserRights(ConscriboService $service, User &$user): void
    {
        // Get groups
        $groups = $this->getGroupMembers($service);
        $admins = $this->getAdminMembers($service);

        // Assign
        $conscriboId = $user->conscribo_id;
        $user->is_voter = $this->IdInCollection($conscriboId, $groups, self::$voteGroups);
        $user->can_proxy = $this->IdInCollection($conscriboId, $groups, self::$proxyGroups);
        $user->is_admin = $admins->contains($conscriboId);

        // Remove proxy if set and if not allowed to vote
        if (!$user->is_voter && $user->proxy) {
            $user->proxy()->dissociate();
        }

        // Remove foreign proxy if not authorized
        if (!$user->can_proxy || $user->proxyFor !== null) {
            // Remove relation
            $proxy = $user->proxyFor;
            $proxy->proxy()->dissociate();
            $proxy->save();

            // Unbind on model
            $user->proxyFor = null;
        }

        // Disallow voter and proxies to be monitors
        if ($user->is_voter || $user->proxyFor !== null) {
            $user->is_monitor = false;
        }

        // Report judgement
        $judge = 'none';
        if ($user->is_voter) {
            $judge = 'vote and proxy';
        } elseif ($user->can_proxy) {
            $judge = 'proxy only';
        }
        $this->line(
            "User <info>{$user->name}</> vote judgement: <comment>$judge</>.",
            null,
            OutputInterface::VERBOSITY_VERBOSE
        );
    }
}
