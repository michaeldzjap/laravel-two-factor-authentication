# Two-Factor-Authentication
A two-factor authentication package for Laravel 5.4

## Description
This is a two-factor authentication package for *Laravel 5.4*. It is heavily inspired by the [Laravel Two-Factor Authentication](https://github.com/srmklive/laravel-twofactor-authentication) package. The main differences between this package and the aforementioned package are:

- This package currently only works with the *MessageBird Verify* api or the `"null"` driver that goes through all the steps of the two-factor authentication process without actually doing any real verification. This could be useful for testing purposes.
- This package uses throttling to limit the number of unsuccessful authentication attempts in a certain amount of time.
- This package is only guaranteed to work with Laravel 5.4. Prior version have not been tested.

## Installation
1 To install using *Composer* run:
```
composer require michaeldzjap/twofactor-auth
```
If you want to use MessageBird Verify as the two-factor authentication provider (default) then you also need to install the [MessageBird PHP api](https://github.com/messagebird/php-rest-api):
```
composer require messagebird/php-rest-api
```
and don't forget to add your `MESSAGEBIRD_ACCESS_KEY` variable to the `.env`.

2 Add the service provider to the `'providers'` array in `config/app.php`:
```php
MichaelDzjap\TwoFactorAuth\TwoFactorAuthServiceProvider::class
```
3 Run the following *artisan* command to publish the configuration, language and view files:
```
php artisan vendor:publish
```
If you only want to publish only one of these file groups, for instance if you don't need the views or language files, you can append one of the following commands to the *artisan* command: `--tag=config`, `--tag=lang` or `--tag-views`.

4 Run the following *artisan* command to run the database migrations
```
php artisan migrate
```
This will add a `mobile` column to the `users` table and create a `two_factor_auths` table.

5 Add the following trait to your `User` model:
```php
...
use MichaelDzjap\TwoFactorAuth\TwoFactorAuthenticable;

class User extends Authenticatable
{
    use Notifiable, TwoFactorAuthenticable;
...
```
Optionally, you might want to add `'mobile'` to your `$fillable` array.

## Changes to the Login Process
The following two-factor authentication routes will be added automatically:
```php
$router->get('/auth/token', 'App\Http\Controllers\Auth\TwoFactorAuthController@showTwoFactorForm')->name('auth.token');
$router->post('/auth/token', 'App\Http\Controllers\Auth\TwoFactorAuthController@verifyToken');
```
The first route is the route the user will be redirected to once the two-factor authentication process has been initiated. The second route is used to verify the two-factor authentication token that is to be entered by the user. The `showTwoFactorForm` controller method does exactly what it says. There do exist cases where you might want to respond differently however. For instance, instead of loading a view you might just want to return a `json` response. In that case you can simply overwrite `showTwoFactorForm` in the `TwoFactorAuthController` to be discussed below.

1 Add the following trait to `LoginController`:
```php
...
use MichaelDzjap\TwoFactorAuth\Http\Controllers\InitiatesTwoFactorAuthProcess;

class LoginController extends Controller
{
    use AuthenticatesUsers, InitiatesTwoFactorAuthProcess;
...
```
and also add the following functions:
```php
/**
 * The user has been authenticated.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  mixed  $user
 * @return mixed
 */
protected function authenticated(Request $request, $user)
{
    self::shouldTwoFactorAuthenticate($request, $user);
}
```
and
```php
/**
 * Provider specific two-factor authentication logic. In the case of MessageBird
 * we just want to send an authentication token via SMS.
 *
 * @param  User $user
 * @return mixed
 */
private function registerUserAndSendToken(User $user)
{
    // Custom, provider dependend logic for sending an authentication token 
    // to the user. In the case of MessageBird Verify this could simply be
    // app(\MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider::class)->sendSMSToken($this->user)
    // Here we assume this function is called from a queue'd job called
    dispatch(new SendSMSToken($user));
}
```
The body of the second function can be left empty if you do not want to send a two-factor authentication token automatically after a successful login attempt. Instead, you might want the user to instantiate this process from the form him/herself. In that case you would have to add the required route(s) and controller method(s) yourself. The best place for this would be the `TwoFactorAuthController` to be discussed next.

2 Add a `TwoFactorAuthController` in `app/Http/Controllers/Auth` with the following content:
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use MichaelDzjap\TwoFactorAuth\Http\Controllers\TwoFactorAuthenticatesUsers;

class TwoFactorAuthController extends Controller
{
    use TwoFactorAuthenticatesUsers;

    /**
     * Where to redirect users after two-factor authentication passes.
     *
     * @var string
     */
    protected $redirectTo = '/home';
}
```
