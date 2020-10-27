<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\ConscriboService;
use App\Services\Verification\FlashService;
use App\Services\Verification\MessageBirdService;
use App\Services\VerificationService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        // Service that connects to conscribo
        if (Config::get('app.beta') !== true) {
            $this->app->singleton(ConscriboService::class, static fn () => ConscriboService::fromConfig());
        }

        // Service that sends notifications
        $this->app->singleton(VerificationService::class, static function () {
            // Instantiated services
            $services = [];

            // Add flash if not in prod
            if (App::environment('local') || Config::get('app.beta')) {
                $services[] = new FlashService();
            }

            // Add messagebird if key is set and not in beta
            if (Config::get('app.beta') !== true && !empty(Config::get('services.messagebird.access_key'))) {
                $services[] = new MessageBirdService();
            }

            // Return instance
            return new VerificationService($services);
        });
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
