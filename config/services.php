<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'messagebird' => [
        'access_key' => env('MESSAGEBIRD_ACCESS_KEY'),
        'originator' => env('MESSAGEBIRD_ORIGINATOR', 'Gubmo'),
    ],

    'conscribo' => [
        'account' => env('CONSCRIBO_ACCOUNT'),
        'username' => env('CONSCRIBO_USERNAME'),
        'password' => env('CONSCRIBO_PASSWORD'),
    ]

];
