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

    'maxmind' => [
        'account_id' => env('MAXMIND_ACCOUNT_ID'),
        'license_key' => env('MAXMIND_LICENSE_KEY', env('MAXMIND_API_KEY')),
        'host' => env('MAXMIND_HOST', 'geoip.maxmind.com'),
        'timeout' => env('MAXMIND_TIMEOUT', 3),
        'connect_timeout' => env('MAXMIND_CONNECT_TIMEOUT', 2),
        'database_path' => env('MAXMIND_DATABASE_PATH', storage_path('app/maxmind/GeoLite2-City.mmdb')),
        'isp_database_path' => env('MAXMIND_ISP_DATABASE_PATH', storage_path('app/maxmind/GeoIP2-ISP.mmdb')),
        'asn_database_path' => env('MAXMIND_ASN_DATABASE_PATH', storage_path('app/maxmind/GeoLite2-ASN.mmdb')),
        'auto_download' => env('MAXMIND_AUTO_DOWNLOAD', true),
        'auto_download_include_isp' => env('MAXMIND_AUTO_DOWNLOAD_INCLUDE_ISP', false),
        'auto_update_enabled' => env('MAXMIND_AUTO_UPDATE_ENABLED', true),
    ],

];
