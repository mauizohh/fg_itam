<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp API Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "cloud" (Meta WhatsApp Cloud API), "mock" (Local test simulator),
    |            "log" (Logs outbound payloads to Laravel log without calling API)
    |
    */
    'driver' => env('WHATSAPP_DRIVER', 'cloud'),

    /*
    |--------------------------------------------------------------------------
    | Meta Graph API Configuration
    |--------------------------------------------------------------------------
    */
    'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v21.0'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', ''),
    'business_account_id' => env('WHATSAPP_WABA_ID', ''),
    'access_token' => env('WHATSAPP_ACCESS_TOKEN', ''),
    'verify_token' => env('WHATSAPP_VERIFY_TOKEN', 'fg_ithd_webhook_secret_2026'),
    'app_secret' => env('WHATSAPP_APP_SECRET', ''),
    'timeout' => env('WHATSAPP_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------------
    | Automation Options
    |--------------------------------------------------------------------------
    */
    'auto_create_ticket' => env('WHATSAPP_AUTO_CREATE_TICKET', true),
];
