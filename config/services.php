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

    'ziina' => [
        'base_url' => env('ZIINA_BASE_URL', 'https://api-v2.ziina.com/api'),
        'api_key' => env('ZIINA_API_KEY'),
        'test_mode' => env('ZIINA_TEST_MODE', true),
        'webhook_secret' => env('ZIINA_WEBHOOK_SECRET'),
        'fee_percent' => (float) env('ZIINA_FEE_PERCENT', 7.9),
        'fee_fixed' => (float) env('ZIINA_FEE_FIXED', 2),
    ],

    /*
    | Embedded private-course meetings (HMAC integration). Secrets stay server-side only.
    | See public/assets/INTEGRATION.md
    */
    'meeting' => [
        'base_url' => rtrim((string) env('MEETING_BASE_URL', ''), '/'),
        'api_key' => env('MEETING_API_KEY'),
        'api_secret' => env('MEETING_API_SECRET'),
        'webhook_secret' => env('MEETING_WEBHOOK_SECRET'),
        // Local Docker often uses a self-signed cert — set MEETING_SSL_VERIFY=false for local only.
        'verify_ssl' => filter_var(env('MEETING_SSL_VERIFY', true), FILTER_VALIDATE_BOOLEAN),
    ],

    /*
    | Academy trainee/trainer WhatsApp templates (w-hub.4ja.ai — template: acadmy).
    | Project OTP / trabar still use the legacy 4jawaly credentials in WhatsAppOTPService.
    */
    'whatsapp_academy' => [
        'url' => env(
            'WHATSAPP_ACADEMY_URL',
            'https://w-hub.4ja.ai/api/v1/CzmhkBCH/message/template'
        ),
        'token' => env('WHATSAPP_ACADEMY_TOKEN'),
        'template' => env('WHATSAPP_ACADEMY_TEMPLATE', 'acadmy'),
        'namespace' => env('WHATSAPP_ACADEMY_NAMESPACE', 'ce014d4c_0214_4b10_83a1_d4b9f34e2436'),
        'language' => env('WHATSAPP_ACADEMY_LANGUAGE', 'ar'),
        'default_image' => env(
            'WHATSAPP_ACADEMY_DEFAULT_IMAGE',
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=800&q=80'
        ),
    ],

];
