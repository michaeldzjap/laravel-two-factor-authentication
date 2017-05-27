<?php

$router = app('router');

$router->group([
    'middleware' => ['web', 'guest'],
    'namespace' => 'App\Http\Controllers\Auth',
], function () use ($router) {
    $router->get('/auth/token', 'TwoFactorAuthController@showTwoFactorForm')->name('auth.token');
    $router->post('/auth/token', 'TwoFactorAuthController@verifyToken');
});
