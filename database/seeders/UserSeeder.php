<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private const BETA_EMAIL = 'beta.example.com';

    private const USER_LIST = [
        'admin' => ['Admin without votes', false, false, true],
        'admin-vote' => ['Admin with votes', true, true, true],
        'admin-vote' => ['Admin with votes', true, true, true],
        'member' => ['Lid', true, true, false],
        'junior' => ['Begunstiger', false, true, false],
        'observer' => ['Oud-lid', false, false, false],
    ];

    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        User::unguard();
        foreach (self::USER_LIST as $prefix => [$name, $canVote, $canProxy, $canAdmin]) {
            $email = sprintf('%s@%s', $prefix, self::BETA_EMAIL);

            // Find user or create one
            if (User::where('email', $email)->exists()) {
                continue;
            }

            // Factory
            User::factory()->createOne([
                'email' => $email,
                'name' => $name,
                'is_voter' => $canVote,
                'is_admin' => $canAdmin,
                'can_proxy' => $canProxy,
            ]);
        }
        User::reguard();
    }
}
