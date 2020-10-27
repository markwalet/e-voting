<?php

declare(strict_types=1);

namespace App\Services\Verification;

use App\Contracts\SendsNotifications;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Session;

class FlashService implements SendsNotifications
{
    private const VERIFY_CACHE_NAME = 'verify.dummy.%s';

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
