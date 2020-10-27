<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\SendsNotifications;
use App\Models\User;
use App\Services\Traits\ValidatesPhoneNumbers;
use Illuminate\Support\Facades\Log;
use OTPHP\TOTPInterface;

/**
 * Wrapper around sending services
 */
final class VerificationService
{
    use ValidatesPhoneNumbers;

    /**
     * Sending services
     * @var array<SendsNotifications>
     */
    private array $sendServices;

    /**
     * Creates a new service with the given sending service
     * @param array<SendsNotifications> $sendService
     */
    public function __construct(array $sendServices)
    {
        // Check services
        foreach ($sendServices as $service) {
            \assert($service instanceof SendsNotifications, "Got invalid service!");
        }

        // Assign services
        $this->sendServices = $sendServices;
    }

    /**
     * Returns true if the message can be sent
     * @param User $user
     * @return bool
     * @throws InvalidArgumentException
     */
    public function canSend(User $user): bool
    {
        // Clean up number
        $phone = $this->formatPhoneNumber($user->phone);

        // Fail if empty
        if (empty($phone)) {
            return false;
        }

        // Find any service that allows sending
        foreach ($this->sendServices as $service) {
            if ($service->canSendCode($phone)) {
                return true;
            }
        }

        // Nobody wants to send
        return false;
    }

    /**
     * Sends the TOTP message to the user, returns if it was succesfully sent
     * @param User $user
     * @return bool
     * @throws InvalidArgumentException
     */
    public function sendMessage(User $user): bool
    {
        // Verify phone number
        $phone = $this->formatPhoneNumber($user->phone);
        if (empty($phone)) {
            // Log
            Log::info('Phone number for {user} is missing!', compact('user'));

            // Fail
            return false;
        }

        // Verify TOTP exists
        $totp = $user->totp;
        \assert($totp instanceof TOTPInterface);

        // Fail if missing params
        if (!$totp) {
            // Log
            Log::warn('TOTP for {user} is missing!', compact('user'));

            // Fail
            return false;
        }

        // Prep token
        $code = $user->totp->at(time());
        $split = (int) ceil(strlen($code) / 2);
        $tokenInParts = \implode(' ', \str_split($code, $split));

        // Prep message
        $message = sprintf(
            'Je code om in te loggen voor e-voting is %s',
            $tokenInParts
        );

        // Log
        Log::info('Sending message {message} to {phone}', compact('message', 'phone'));

        // Keep track
        $ok = false;

        // Send via each service that allows it
        foreach ($this->sendServices as $service) {
            if (!$service->canSendCode($phone)) {
                continue;
            }

            // Fail if the service failed
            if (!$service->sendVerificationCode($phone, $message)) {
                return false;
            }

            // Set OK
            $ok = true;
        }

        // True if at least one service sent a message
        return $ok;
    }
}
