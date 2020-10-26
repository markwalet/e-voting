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
     * Sends the user's verification code to the user
     * @param User $user
     * @return bool
     */
    public function sendVerificationCode(User $user): bool;
}
