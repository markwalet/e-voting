<?php

declare(strict_types=1);

namespace App\Providers;

use App\Http\Controllers\AuditController;
use App\Services\ConscriboService;
use App\Services\Verification\FlashService;
use App\Services\Verification\MessageBirdService;
use App\Services\VerificationService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
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
        /**
         * Returns the git version, which then gets baked into
         * the template, allowing it to run just once
         */
        Blade::directive('version', function ($expr) {
            $version = $this->app->make(AuditController::class)->getAppVersion();
            if ($expr === 'link' && $version === 'onbekend') {
                return "https://github.com/gumbo-millennium/e-voting";
            } elseif ($version === 'onbekend') {
                return $version;
            } elseif ($expr === 'link') {
                return "https://github.com/gumbo-millennium/e-voting/tree/{$version}";
            } elseif ($expr === 'short' || $expr === 'true') {
                return substr($version, 0, 7);
            }

            return $version;
        });
    }
}
