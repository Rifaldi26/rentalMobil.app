<?php

return [

    // ─── Existing services ────────────────────────────────────────

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // ─── Midtrans (Payment Gateway) ───────────────────────────────

    'midtrans' => [
        'server_key'    => env('MIDTRANS_SERVER_KEY'),
        'client_key'    => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

        /**
         * URL script Snap — berbeda antara sandbox dan production.
         * Sandbox : https://app.sandbox.midtrans.com/snap/snap.js
         * Production: https://app.midtrans.com/snap/snap.js
         */
        'snap_url'      => env(
            'MIDTRANS_SNAP_URL',
            'https://app.sandbox.midtrans.com/snap/snap.js'
        ),

        /**
         * Notification URL untuk webhook.
         * Wajib diset di Midtrans Dashboard → Settings → Payment → Notification URL
         */
        'notification_url' => env('APP_URL') . '/webhook/midtrans',
    ],
    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URL'),
    ],

];
