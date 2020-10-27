<?php

declare(strict_types=1);

namespace App\Services\Verification;

use App\Contracts\SendsNotifications;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Session;
use MessageBird\Client as MessagebirdClient;
use RuntimeException;

class FlashService implements SendsNotifications
{
    private const VERIFY_CACHE_NAME = 'verify.dummy.%s';

    /**
     * Returns clients or throws a fit if token is unset
     * @return MessagebirdClient
     * @throws RuntimeException
     */
    private static function buildMessagebirdClient(): MessagebirdClient
    {
        // Get token
        $accessToken = Config::get('services.messagebird.access_key');

        // Skip if invalid
        if (!$accessToken) {
            throw new RuntimeException('Failed to get MessageBird instance');
        }

        // Create and return new instance
        try {
            return new MessagebirdClient($accessToken);
        } catch (\Throwable $e) {
            throw new RuntimeException('Failed to get MessageBird instance', 0, $e);
        }
    }

    private ?MessagebirdClient $messageBird = null;

    /**
     * Makes a new service, using the given client if specified. A client
     * will be created from config if not set.
     * @param null|MessagebirdClient $client
     * @return void
     */
    public function __construct(?MessagebirdClient $client = null)
    {
        // Assign client
        $this->messageBird = $client ?? self::buildMessagebirdClient();
    }

    /**
     * @inheritdoc
     */
    public function canSendCode(string $recipient): bool
    {
        // Check cache
        $key = sprintf(self::VERIFY_CACHE_NAME, $recipient);
        return !Cache::has($key) || Cache::get($key) < Date::now();
    }

    /**
     * @inheritdoc
     */
    public function sendVerificationCode(string $recipient, string $message): bool
    {
        // Fail if missing params
        if (empty($recipient) || empty($message)) {
            return false;
        }

        // Prep a clean message
        $cleanMessage = sprintf(
            'To [%s]: %s',
            substr(md5($recipient), 0, 6),
            $message
        );

        // Store in session
        Session::put('debug-message', $cleanMessage);

        // Lock recipient for a while
        $key = sprintf(self::VERIFY_CACHE_NAME, $recipient);
        Cache::put($key, Date::now()->addSeconds(90));

        // Send OK
        return true;
    }
}
