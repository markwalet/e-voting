<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ConscriboService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ConscriboService::class, static fn () => ConscriboService::fromConfig());
    }

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        //
    }
}
