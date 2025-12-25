<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS notifications including provider settings,
    | rate limiting, and feature flags.
    |
    */

    'enabled' => env('SMS_NOTIFICATIONS_ENABLED', true),

    'provider' => [
        'default' => env('SMS_PROVIDER', 'fourjawaly'),
        'fourjawaly' => [
            'enabled' => env('FOURJAWALY_ENABLED', true),
            'base_url' => env('FORJAWALY_URL', 'https://api-sms.4jawaly.com/api/v1/'),
            'api_key' => env('FORJAWALY_API_KEY'),
            'api_secret' => env('FORJAWALY_API_SECRET'),
            'sender' => env('FORJAWALY_SENDER', 'TechPack'),
        ],
    ],

    'rate_limiting' => [
        'enabled' => env('SMS_RATE_LIMITING_ENABLED', true),
        'max_per_minute' => env('SMS_MAX_PER_MINUTE', 5),
        'max_per_hour' => env('SMS_MAX_PER_HOUR', 20),
        'max_per_day' => env('SMS_MAX_PER_DAY', 100),
    ],

    'retry' => [
        'max_attempts' => env('SMS_MAX_ATTEMPTS', 3),
        'backoff_seconds' => env('SMS_BACKOFF_SECONDS', 60),
    ],

    'logging' => [
        'enabled' => env('SMS_LOGGING_ENABLED', true),
        'retention_days' => env('SMS_LOG_RETENTION_DAYS', 90),
        'mask_phone' => env('SMS_MASK_PHONE', true),
    ],

    'admin' => [
        'recipients' => env('ADMIN_SMS_RECIPIENTS', ''),
        'enabled_events' => [
            'vendor_registered' => env('ADMIN_SMS_VENDOR_REGISTERED', true),
            'new_order' => env('ADMIN_SMS_NEW_ORDER', true),
        ],
    ],

    'templates' => [
        'locale' => env('SMS_DEFAULT_LOCALE', 'ar'),
        'fallback_locale' => env('SMS_FALLBACK_LOCALE', 'en'),
    ],

    'verification' => [
        'code_length' => env('VERIFICATION_CODE_LENGTH', 6),
        'expires_in_minutes' => env('VERIFICATION_EXPIRES_IN', 10),
        'max_attempts' => env('VERIFICATION_MAX_ATTEMPTS', 5),
    ],
];

