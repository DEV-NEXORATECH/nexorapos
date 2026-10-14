<?php

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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'hostinger' => [
        'api_token' => env('HOSTINGER_API_TOKEN'),
        'domain' => env('HOSTINGER_DOMAIN', 'karirandalan.com'),
        'record_type' => env('HOSTINGER_DNS_RECORD_TYPE', 'CNAME'),
        'record_target' => env('HOSTINGER_DNS_RECORD_TARGET'),
        'ttl' => (int) env('HOSTINGER_DNS_TTL', 300),
    ],

];
