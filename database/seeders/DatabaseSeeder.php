<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @return void
     */
    public function run()
    {
        // Seed if in beta
        if (Config::get('app.beta')) {
            // Create test users
            $this->call(UserSeeder::class);
        }

        // If local, create test polls
        if (App::environment('local')) {
            $this->call(PollSeeder::class);
        }
    }
}
