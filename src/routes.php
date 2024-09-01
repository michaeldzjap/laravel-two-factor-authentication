<?php

$router = app('router');

$router->group([
    'middleware' => ['web', 'guest'],
    'namespace' => config('twofactor-auth.routes.namespace', 'App\Http\Controllers\Auth'),
], function () use ($router) {
    $router->get(
        config('twofactor-auth.routes.get.url'),
        'TwoFactorAuthController@showTwoFactorForm'
    )->name(config('twofactor-auth.routes.get.name'));
    $router->post(config('twofactor-auth.routes.post'), 'TwoFactorAuthController@verifyToken');
});
