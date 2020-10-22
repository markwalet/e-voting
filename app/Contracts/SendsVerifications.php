<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

interface SendsVerifications
{
    /**
     * Checks if $phoneNumber can receive a new token, to allow for throttling
     * @param User $user
     * @return bool
     */
    public function canSendCode(User $user): bool;

    /**
     * Sends the verification code to the given phone number
     * @param User $user
     * @param string $code
     * @return bool
     */
    public function sendVerificationCode(User $user, string $code): bool;
}
