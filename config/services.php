<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Credentials and settings for external services belong here so application
    | code can use config() instead of reading environment variables directly.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Platform License M-Pesa
    |--------------------------------------------------------------------------
    |
    | Used only for ISP Admin -> Super Admin platform license payments.
    | Secrets remain in .env and are exposed to application code through
    | Laravel's configuration layer.
    |
    */

    'license_mpesa' => [
        'environment' => env(
            'LICENSE_MPESA_ENVIRONMENT',
            'sandbox'
        ),

        'consumer_key' => env(
            'LICENSE_MPESA_CONSUMER_KEY'
        ),

        'consumer_secret' => env(
            'LICENSE_MPESA_CONSUMER_SECRET'
        ),

        'shortcode' => env(
            'LICENSE_MPESA_SHORTCODE'
        ),

        'passkey' => env(
            'LICENSE_MPESA_PASSKEY'
        ),

        'transaction_type' => env(
            'LICENSE_MPESA_TRANSACTION_TYPE',
            'CustomerPayBillOnline'
        ),

        'callback_url' => env(
            'LICENSE_MPESA_CALLBACK_URL'
        ),

        'timeout' => 30,
    ],

];