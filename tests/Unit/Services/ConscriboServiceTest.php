<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\ConscriboService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class ConscriboServiceTest extends TestCase
{
    private const FILE_PATH = 'tests/resources/conscribo/';

    /**
     * Ensure the config is predictabe
     * @return void
     * @before
     */
    public function registerConfig(): void
    {
        // Update config to something predictable
        $this->afterApplicationCreated(static fn () => Config::set([
            'services.conscribo.account' => 'unit-test',
            'services.conscribo.username' => 'test-user',
            'services.conscribo.password' => 'test-pass'
        ]));
    }

    /**
     * Register the given JSON files
     * @param array<string> $path
     * @return void
     */
    public function registerFakes(string ...$paths): void
    {
        // Build a path
        $basePath = \base_path(self::FILE_PATH);

        // Build a sequence for each path
        $fake = Http::fakeSequence();
        foreach ($paths as $file) {
            $fake->pushFile($basePath . Str::start($file, '/'));
        }

        // Fake the HTTP client
        Http::fake([
            // Fake the request
            'secure.conscribo.nl/unit-test/request.json' => $fake,

            // Fail on the rest
            '*' => Http::response(null, Response::HTTP_BAD_REQUEST)
        ]);
    }

    /**
     * Test a regular login
     * @return void
     */
    public function testAuthentication()
    {
        // Mock HTTP
        $this->registerFakes('http-auth-valid.json');

        // Get service
        $service = App::make(ConscriboService::class);
        \asset($service instanceof ConscriboService);

        // Authenticate
        $service->authenticate();

        // Check if the request was sent
        Http::assertSentCount(1);
        Http::assertSent(static function ($request) {
            dd($request);
        });
    }
}
