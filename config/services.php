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

    'midtrans' => [
        'is_production' => filter_var(env('MIDTRANS_IS_PRODUCTION', false), FILTER_VALIDATE_BOOL),
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
        'expiry_minutes' => (int) env('MIDTRANS_EXPIRY_MINUTES', 15),
        'qris_acquirer' => env('MIDTRANS_QRIS_ACQUIRER', 'gopay'),
    ],

    'fonnte' => [
        'enabled' => filter_var(env('FONNTE_ENABLED', false), FILTER_VALIDATE_BOOL),
        'token' => env('FONNTE_TOKEN'),
        'base_url' => env('FONNTE_BASE_URL', 'https://api.fonnte.com'),
        'country_code' => env('FONNTE_COUNTRY_CODE', '62'),
        'default_delay' => (int) env('FONNTE_DEFAULT_DELAY', 0),
        'default_typing' => filter_var(env('FONNTE_DEFAULT_TYPING', false), FILTER_VALIDATE_BOOL),
        'default_group_target' => env('FONNTE_DEFAULT_GROUP_TARGET'),
    ],

];
