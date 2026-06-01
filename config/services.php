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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'tpinklab' => [
        'api_url'                  => env('TPINKLAB_API_URL', 'https://tpinklab.com/brand-partner/send-so'),
        'api_key'                  => env('TPINKLAB_API_KEY'),
        'admin_api_url'            => env('TPINKLAB_ADMIN_API_URL', 'http://localhost:8000/api/orders'),
        'brandpartner_callback_url' => env('TPINKLAB_BRANDPARTNER_CALLBACK_URL', 'http://localhost:8001/api/products'),
    ],

    'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),

    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'xendit' => [
    'secret_key'     => env('XENDIT_SECRET_KEY'),
    'webhook_secret' => env('XENDIT_WEBHOOK_SECRET'),
    ],
    
];
