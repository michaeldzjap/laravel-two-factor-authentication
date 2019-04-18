<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Options:
    | - 'enabled': always require two-factor authentication
    | - 'disabled': disabled, never require two-factor authentication
    | - 'user': look if a row exists in the two_factor_auths table for the
    |   user
    |
    */

    'enabled' => 'user',

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

    'default' => env('TWO_FACTOR_AUTH_DRIVER', 'null'),

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

    ],

    /*
    |--------------------------------------------------------------------------
    | Routes Configuration + Naming
    |--------------------------------------------------------------------------
    |
    | Here you may customize the route URL's and in case of the GET route, also
    | the route name.
    |
    */

    'routes' => [
        'get' => [
            'url' => '/auth/token',
            'name' => 'auth.token',
        ],
        'post' => '/auth/token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Model setttings
    |--------------------------------------------------------------------------
    |
    | \App\User is used, but its primary key can be specified
    |
    */

    'models' => [
        'user' => [
            'pk' => 'id'
        ]
    ],

];
