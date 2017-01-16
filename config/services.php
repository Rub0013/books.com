<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],
    'facebook' => [
        'client_id' => '1615779115392025',
        'client_secret' => 'b799dbba5f89092eeb0b207cd97b2694',
        'redirect' => 'http://books.com/facebook/callback'
    ],
    'google' => [
        'client_id' => '739862729543-jrdj8o0v0nm3t8jlso2n96uoaoroc9j6.apps.googleusercontent.com',
        'client_secret' => 'Prx00Q3FXTcffQHLokOgqLco',
        'redirect' => 'http://books.com/google/callback'
    ],
];
