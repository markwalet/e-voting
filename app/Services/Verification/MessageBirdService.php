<?php

declare(strict_types=1);

namespace App\Services\Verification;

use App\Contracts\SendsNotifications;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use MessageBird\Client as MessagebirdClient;
use MessageBird\Exceptions\HttpException;
use MessageBird\Objects\Message;
use RuntimeException;

class MessageBirdService implements SendsNotifications
{
    private const VERIFY_CACHE_NAME = 'verify.messagebird.%s';

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
    public function sendVerificationCode(string $recipient, string $body): bool
    {
        // Fail if missing params
        if (empty($recipient) || empty($body)) {
            return false;
        }

        // Get verification
        $message = new Message();
        $message->originator = Config::get('services.messagebird.originator');
        $message->recipients = [intval(ltrim($recipient, '+'))];
        $message->validity = 60; // Allow 60 seconds for sending
        $message->body = $body;

        // Log what we're sending
        Log::info("Sending {message} to {recipient}.", [
            'message' => $body,
            'recipient' => $recipient
        ]);

        // Send verification
        try {
            // Send
            $result = $this->messageBird->messages->create($message);

            // Lock user
            $key = sprintf(self::VERIFY_CACHE_NAME, $recipient);
            Cache::put($key, Date::now()->addSeconds(90));

            // OK if actually sent
            return $result instanceof Message && $result->getId() !== null;
        } catch (HttpException $exception) {
            // Report failure in log
            Log::warn('Login token sending failed: {exception}', [
                'exception' => $exception,
                'phone' => $recipient
            ]);

            // Fail
            return false;
        }
    }
}
