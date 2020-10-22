<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // User policies
        Gate::define('view', static fn ($user) => $user->exists);
        Gate::define('vote', static fn ($user) => $user->is_voter);
        Gate::define('admin', static fn ($user) => $user->is_admin);
        Gate::define('monitor', static fn ($user) => $user->is_monitor || $user->is_admin);
    }
}
