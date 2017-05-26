<?php

$router = app('router');

$router->group(['middleware' => 'web'], function () use ($router) {
    $router->get('/auth/token', 'App\Http\Controllers\Auth\TwoFactorAuthController@showTwoFactorForm')->name('auth.token');
    $router->post('/auth/token', 'App\Http\Controllers\Auth\TwoFactorAuthController@verifyToken');
});
