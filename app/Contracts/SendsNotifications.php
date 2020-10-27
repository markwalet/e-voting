<?php

declare(strict_types=1);

namespace App\Contracts;

interface SendsNotifications
{
    /**
     * Check if the given recipient can receive a new code
     * @param string $recipient
     * @return bool
     */
    public function canSendCode(string $recipient): bool;

    /**
     * Sends a message to the given phone number
     * @param string $recipient Phone number, in E164 format
     * @param string $message The message to send
     * @return bool True if sending went OK
     */
    public function sendVerificationCode(string $recipient, string $message): bool;
}
