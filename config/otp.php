<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Expiry Time
    |--------------------------------------------------------------------------
    |
    | This value determines the number of minutes an OTP code will remain valid
    | before it expires.
    |
    */

    'expiry_minutes' => env('OTP_EXPIRY_MINUTES', 5),

    /*
    |--------------------------------------------------------------------------
    | OTP Length
    |--------------------------------------------------------------------------
    |
    | The number of digits in the OTP code.
    |
    */

    'length' => env('OTP_LENGTH', 6),

    /*
    |--------------------------------------------------------------------------
    | SMS Provider
    |--------------------------------------------------------------------------
    |
    | The SMS gateway provider to use for sending OTP codes.
    | Supported: "log", "twilio", "vonage", "orange", "mtn"
    |
    */

    'sms_provider' => env('SMS_PROVIDER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | SMS Provider Credentials
    |--------------------------------------------------------------------------
    */

    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from' => env('TWILIO_FROM'),
    ],

    'vonage' => [
        'api_key' => env('VONAGE_API_KEY'),
        'api_secret' => env('VONAGE_API_SECRET'),
        'from' => env('VONAGE_FROM'),
    ],

    'orange' => [
        'client_id' => env('ORANGE_CLIENT_ID'),
        'client_secret' => env('ORANGE_CLIENT_SECRET'),
        'from' => env('ORANGE_FROM'),
    ],

    'mtn' => [
        'api_key' => env('MTN_API_KEY'),
        'api_secret' => env('MTN_API_SECRET'),
        'from' => env('MTN_FROM'),
    ],

];

