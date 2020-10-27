<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
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
            $this->call(UserSeeder::class);
        }
        // \App\Models\User::factory(10)->create();
    }
}
