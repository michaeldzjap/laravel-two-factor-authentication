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
    | Database Settings
    |--------------------------------------------------------------------------
    |
    | Starting from Laravel 5.8, the default is to use "bigIncrements" instead
    | of "increments" for the "id" column on the "users" table. This setting
    | allows you to control what type to use for the "user_id" column on the
    | "two_factor_auths" table. The default is to use "unsignedBigInteger" in
    | order to stay in line with the changes in Laravel 5.8.
    |
    | NOTE: Modifying this setting only has an effect before you run any
    | migrations for this package. If you need to change the signature of
    | "user_id" afterwards, you will have to write your own migration for this
    | (see install instructions for more details).
    |
    */

    'big_int' => true,

];
