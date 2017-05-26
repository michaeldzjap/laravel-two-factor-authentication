<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Two-Factor Authentication Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default two-factor authentication provider that
    | will be used by the framework when a user needs to be two-factor
    | authenticated. You may set this to any of the connections defined in the
    | "providers" array below.
    |
    | Supported: "messagebird", "null"
    |
    */

    'default' => env('TWO_FACTOR_AUTH_DRIVER', 'messagebird'),

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication Providers
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the two-factor authentication providers that
    | will be used to two-factor authenticate the user.
    |
    */

    'providers' => [

        'messagebird' => [
            'driver' => 'messagebird',
            'key' => env('MESSAGEBIRD_ACCESS_KEY'),
            'options' => [
                'originator' => 'Me',
                'timeout' => 60,
                'language' => 'nl-nl',
            ],
        ],

        'null' => [
            'driver' => 'null',
        ],

    ]

];
