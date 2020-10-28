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
        \App\Models\Poll::class => \App\Policies\PollPolicy::class,
        \App\Models\PollApproval::class => \App\Policies\PollApprovalPolicy::class,
        \App\Models\User::class => \App\Policies\UserPolicy::class,
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
        Gate::define('vote', static fn ($user) => (
            $user->is_present && (
                ($user->is_voter && $user->proxy_id === null) ||
                ($user->can_proxy && $user->proxyFor !== null)
            )
        ));
        Gate::define('admin', static fn ($user) => $user->is_admin);
        Gate::define('monitor', static fn ($user) => $user->is_monitor);
    }
}
