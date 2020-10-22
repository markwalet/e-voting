<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\SendsVerifications;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use MessageBird\Client as MessagebirdClient;
use MessageBird\Exceptions\HttpException;
use MessageBird\Objects\Message;
use OTPHP\TOTPInterface;
use RuntimeException;

class MessageBirdService implements SendsVerifications
{
    private const VERIFY_CACHE_NAME = 'messagebird.verify.%s';

    private const PHONE_COUNTRY = 'NL';

    private const VALID_NUMBER_TYPES = [
        PhoneNumberType::FIXED_LINE_OR_MOBILE,
        PhoneNumberType::FIXED_LINE,
        PhoneNumberType::MOBILE,
        PhoneNumberType::PERSONAL_NUMBER,
        PhoneNumberType::UAN,
        PhoneNumberType::VOIP,
    ];

    private ?MessagebirdClient $messageBird = null;

    /**
     * Returns clients or throws a fit if token is unset
     * @return MessagebirdClient
     * @throws RuntimeException
     */
    private function getMessagebird(): MessagebirdClient
    {
        // Get token
        $accessToken = Config::get('services.messagebird.access_key');

        // Skip if invalid
        if (!$accessToken) {
            throw new RuntimeException('Failed to get MessageBird instance');
        }

        // Make instance if required
        if (!$this->messageBird) {
            try {
                $this->messageBird = new MessagebirdClient($accessToken);
            } catch (\Throwable $e) {
                throw new RuntimeException('Failed to get MessageBird instance', 0, $e);
            }
        }

        // Done
        return $this->messageBird;
    }

    /**
     * Formats a number to a valid phone number, if it's valid
     * @param null|string $phone
     * @return null|string
     * @throws InvalidArgumentException
     */
    private function toValidNumber(?string $phone): ?string
    {
        // Skip if empty
        if (empty($phone)) {
            return null;
        }

        // Get instance
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Parse
            $phoneObject = $phoneUtil->parse($phone, self::PHONE_COUNTRY);

            if (
                // Validate phone
                !$phoneUtil->isValidNumber($phoneObject) ||
                // Validate region
                !$phoneUtil->isValidNumberForRegion($phoneObject, self::PHONE_COUNTRY) ||
                // Validate type
                !\in_array($phoneUtil->getNumberType($phoneObject), self::VALID_NUMBER_TYPES)
            ) {
                return null;
            }

            // Return as E164 phone number (+31612345678)
            return $phoneUtil->format($phoneObject, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function canSendCode(User $user): bool
    {
        // Get number
        $phoneNumber = $this->toValidNumber($user->phone);

        // Fail if not set
        if (!$phoneNumber) {
            return false;
        }

        // Check cache
        $key = sprintf(self::VERIFY_CACHE_NAME, $user->id);
        return !Cache::has($key) || Cache::get($key) < Date::now();
    }


    /**
     * Sends the most recent code to the server
     * @param User $user
     * @return bool
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws RequestException
     * @throws ServerException
     */
    public function sendVerificationCode(User $user): bool
    {
        // Fail if impossible
        if (!$this->canSendCode($user)) {
            return false;
        }

        // Get number and totp
        $phoneNumber = $this->toValidNumber($user->phone);
        $totp = $user->totp;
        \assert($totp instanceof TOTPInterface);

        // Fail if missing params
        if (!$phoneNumber || !$totp) {
            return false;
        }

        // Get client
        $client = $this->getMessagebird();

        // Get verification
        $message = new Message();
        $message->originator = Config::get('services.messagebird.originator');
        $message->recipients = [intval(ltrim($phoneNumber, '+'))];
        $message->validity = 60; // Allow 60 seconds for sending
        $message->body = sprintf(
            'Je code om in te loggen voor e-voting is %s',
            $user->totp->at(time())
        );

        // Send verification
        try {
            // Send
            $result = $client->messages->create($message);

            // Lock user
            $key = sprintf(self::VERIFY_CACHE_NAME, $user->id);
            Cache::put($key, Date::now()->addSeconds(90));

            // OK if actually sent
            return $result instanceof Message && $result->getId() !== null;
        } catch (HttpException $exception) {
            // Report failure in log
            Log::warn('Login token sending failed: {exception}', [
                'exception' => $exception,
                'phone' => $phoneNumber
                ]);

            // Fail
            return false;
        }
    }
}
