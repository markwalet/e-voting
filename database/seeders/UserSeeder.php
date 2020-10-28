<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private const BETA_EMAIL = 'beta.example.com';

    /**
     * Random accounts. The numbers are randomly picked from
     * wieheeftgebeld.nl to represent actual data (otherwise
     * the validation will reject them).
     */
    private const USER_LIST = [
        'admin' => [
            'name' => 'Admin without votes',
            'phone' => '06 13 736 942',
            'is_admin' => true,
        ],
        'admin-vote' => [
            'name' => 'Admin with votes',
            'phone' => '+31 (0)6 49673016',
            'is_voter' => true,
            'is_admin' => true,
            'can_proxy' => true,
        ],
        'admin-vote' => [
            'name' => 'Admin with votes',
            'phone' => '613241827',
            'is_voter' => true,
            'is_admin' => true,
            'can_proxy' => true,
        ],
        'member' => [
            'name' => 'Lid',
            'phone' => '+31 232661050',
            'is_voter' => true,
            'can_proxy' => true,
        ],
        'junior' => [
            'name' => 'Begunstiger',
            'phone' => '+31 613473336',
            'can_proxy' => true,
        ],
        'observer' => [
            'name' => 'Oud-lid',
            'phone' => '0031 (0)6 49 67 30 16',
        ],
        'monitor' => [
            'name' => 'Monitor lid',
            'phone' => '0031 (0)6 49 66 30 16',
            'is_monitor' => true
        ],
    ];

    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        User::unguard();
        foreach (self::USER_LIST as $prefix => $data) {
            $email = sprintf('%s@%s', $prefix, self::BETA_EMAIL);

            // Find user or create one
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::factory()->makeOne(array_merge(
                    compact('email'),
                    $data
                ));
            }

            // Seed some data
            $user->fill($data);

            // Save it, allowing for events
            $user->save();
        }
        User::reguard();
    }
}
