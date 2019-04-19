[![Latest Stable Version](https://poser.pugx.org/michaeldzjap/twofactor-auth/version)](https://packagist.org/packages/michaeldzjap/twofactor-auth)
[![Total Downloads](https://poser.pugx.org/michaeldzjap/twofactor-auth/downloads)](https://packagist.org/packages/michaeldzjap/twofactor-auth)
[![Latest Unstable Version](https://poser.pugx.org/michaeldzjap/twofactor-auth/v/unstable)](//packagist.org/packages/michaeldzjap/twofactor-auth)
[![License](https://poser.pugx.org/michaeldzjap/twofactor-auth/license)](https://packagist.org/packages/michaeldzjap/twofactor-auth)

# laravel-two-factor-authentication
A two-factor authentication package for _Laravel_ >= 5.5

## Description
This is a two-factor authentication package for _Laravel_. It is heavily inspired by the [Laravel Two-Factor Authentication](https://github.com/srmklive/laravel-twofactor-authentication) package. The main differences between this package and the aforementioned package are:

- This package currently only works out of the box with the *MessageBird Verify* api or the `'null'` driver that goes through all the steps of the two-factor authentication process without actually doing any real verification. This could be useful for testing purposes. You can however, specify a custom provider yourself.
- This package uses throttling to limit the number of unsuccessful authentication attempts in a certain amount of time.
- The current version of this package is only guaranteed to work with _Laravel_ >= 5.5. Version 1.* of this package works with _Laravel_ 5.4. Versions of _Laravel_ prior to 5.4 have not been tested.

## Important
From _Laravel_ 5.8 and onwards, the default is to use `bigIncrements` instead of `increments` for the `id` column on the `users` table. As such, the default for this package is to use the same convention for the `user_id` column on the `two_factor_auths` table. If this is not what you want, you can change this to your liking by modifying the migration files that are published for this package.

Publishing the package's migration files allows for more flexibility with regards to customising your database structure. However, it could also cause complications if you already have ran migrations as part of installing previous versions of this package. In this case you simply might want to bypass running the migrations again or only run them when in a specific environment.

### Optional correction
Versions of this package prior to v2.3.0 incorrectly created the `user_id` column on the `two_factor_auths` table using `increments` instead of `unsignedInteger`. Practically speaking, this error is of no concern. Although there is no need to have a _primary_ key for the `user_id` column, it doesn't cause any problems either. However, if for some reason you don't like this idea, it is safe to remove the _primary_ key using a migration of the form

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovePrimaryFromTwoFactorAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('two_factor_auths', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('two_factor_auths', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->change();
            $table->dropPrimary(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('two_factor_auths', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('two_factor_auths', function (Blueprint $table) {
            $table->increments('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
```
Note that you will need the [doctrine/dbal](https://packagist.org/packages/doctrine/dbal) package for this migration to work. Furthermore, if the `id` column on your `users` table is of type `bigIncrements` you will have to change the lines `$table->unsignedInteger('user_id')->change();` to `$table->unsignedBigInteger('user_id')->change();` and `$table->increments('user_id')->change();` to `$table->bigIncrements('user_id')->change();` respectively.

## Installation
1. To install using *Composer* run:
```
composer require michaeldzjap/twofactor-auth
```
If you want to use MessageBird Verify as the two-factor authentication provider then you also need to install the [MessageBird PHP api](https://github.com/messagebird/php-rest-api):
```
composer require messagebird/php-rest-api
```
and don't forget to add your `MESSAGEBIRD_ACCESS_KEY` and `TWO_FACTOR_AUTH_DRIVER=messagebird` variables to the `.env`. If you instead wish to use the `'null'` driver (default) then do **NOT** define the `TWO_FACTOR_AUTH_DRIVER` variable in your `.env`.

2. Add the service provider to the `'providers'` array in `config/app.php`:
```php
MichaelDzjap\TwoFactorAuth\TwoFactorAuthServiceProvider::class
```
3. Run the following *artisan* command to publish the configuration, language and view files:
```
php artisan vendor:publish
```
If you want to publish only one of these file groups, for instance if you don't need the views or language files, you can append one of the following commands to the *artisan* command: `--tag=config`, `--tag=lang` or `--tag-views`.

4. **Important**: Make sure you do this step _before_ you run any migrations for this package, as otherwise it might give you unexpected results.

    From _Laravel_ 5.8 and on, the default is to use `bigIncrements` instead of `increments` for the `id` column on the `users` table. As such, the default for this package is to use the same convention for the `user_id` column on the `two_factor_auths` table. If this is not what you want, you can modify the published migration files for this package.

5. Run the following *artisan* command to run the database migrations
```
php artisan migrate
```
This will add a `mobile` column to the `users` table and create a `two_factor_auths` table.

6. Add the following trait to your `User` model:
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
$router->group([
    'middleware' => ['web', 'guest'],
    'namespace' => 'App\Http\Controllers\Auth',
], function () use ($router) {
    $router->get('/auth/token', 'TwoFactorAuthController@showTwoFactorForm')->name('auth.token');
    $router->post('/auth/token', 'TwoFactorAuthController@verifyToken');
});
```
The first route is the route the user will be redirected to once the two-factor authentication process has been initiated. The second route is used to verify the two-factor authentication token that is to be entered by the user. The `showTwoFactorForm` controller method does exactly what it says. There do exist cases where you might want to respond differently however. For instance, instead of loading a view you might just want to return a `json` response. In that case you can simply overwrite `showTwoFactorForm` in the `TwoFactorAuthController` to be discussed below.

1. Add the following import to `LoginController`:
```php
...
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;

class LoginController extends Controller
{
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
    if (resolve(TwoFactorProvider::class)->enabled($user)) {
        return self::startTwoFactorAuthProcess($request, $user);
    }

    return redirect()->intended($this->redirectPath());
}
```
and
```php
/**
 * Log out the user and start the two factor authentication state.
 *
 * @param  \Illuminate\Http\Request $request
 * @param  \App\User $user
 * @return \Illuminate\Http\Response
 */
private function startTwoFactorAuthProcess(Request $request, $user)
{
    // Logout user, but remember user id
    auth()->logout();
    $request->session()->put(
        'two-factor:auth', array_merge(['id' => $user->id], $request->only('email', 'remember'))
    );

    self::registerUserAndSendToken($user);

    return redirect()->route('auth.token');
}
```
and lastly
```php
/**
 * Provider specific two-factor authentication logic. In the case of MessageBird
 * we just want to send an authentication token via SMS.
 *
 * @param  \App\User $user
 * @return mixed
 */
private function registerUserAndSendToken(User $user)
{
    // Custom, provider dependend logic for sending an authentication token
    // to the user. In the case of MessageBird Verify this could simply be
    // resolve(TwoFactorProvider::class)->sendSMSToken($this->user)
    // Here we assume this function is called from a queue'd job
    dispatch(new SendSMSToken($user));
}
```
You can discard the third function if you do not want to send a two-factor authentication token automatically after a successful login attempt. Instead, you might want the user to instantiate this process from the form him/herself. In that case you would have to add the required route and controller method to trigger this function yourself. The best place for this would be the `TwoFactorAuthController` to be discussed next.

2. Add a `TwoFactorAuthController` in `app/Http/Controllers/Auth` with the following content:
```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use MichaelDzjap\TwoFactorAuth\Http\Controllers\TwoFactorAuthenticatesUsers;

class TwoFactorAuthController extends Controller
{
    use TwoFactorAuthenticatesUsers;

    /**
     * The maximum number of attempts to allow.
     *
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * The number of minutes to throttle for.
     *
     * @var int
     */
    protected $decayMinutes = 1;

    /**
     * Where to redirect users after two-factor authentication passes.
     *
     * @var string
     */
    protected $redirectTo = '/home';
}
```
3. If you want to give textual feedback to the user when two-factor authentication fails due to an expired token or when throttling kicks in you may want to add this to `resources/views/auth/login.blade.php`:
```php
...
<form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Add this block to show an error message in case of an expired token or user lockout --}}
    @if ($errors->has('token'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{ $errors->first('token') }}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
...
```

## Using a Custom Provider
Since the v2.1.0 release it is possible to user your own custom provider. To do so your provider needs to implement `MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider` (and possibly `MichaelDzjap\TwoFactorAuth\Contracts\SMSToken` if you want to send the authentication token via SMS).

1. Assuming the name of your custom provider is 'dummy', you should register it with `TwoFactorAuthManager` from a service provider (could be `\App\Providers\AppServiceProvider`):
```php
resolve(\MichaelDzjap\TwoFactorAuth\TwoFactorAuthManager)->extend('dummy', function ($app) {
    return new DummyProvider;
});
```
2. Add an entry for you custom provider in the 'provider' array in *app/config/twofactor-auth.php*:
```php
...
'dummy' => [

    'driver' => 'dummy',

],
...
```
3. Lastly, don't forget to change the name of the provider in your *.env*:
```
TWO_FACTOR_AUTH_DRIVER=dummy
```

## Errors and Exceptions
Unfortunately the *MessageBird* php api throws rather generic exceptions when the verification of a token fails. The only way to distinguish an expired token from an invalid token is by comparing their error messages, which obviously is not a very robust mechanism. The reason this is rather unfortunate is because in the case of an invalid token we want to give the user at least a few (3) changes to re-enter the token before throttling kicks in, whereas in the case of an expired token we just want to redirect to the login screen right away.

## Testing
An example project including unit and browser tests can be found [here](https://github.com/michaeldzjap/laravel-two-factor-authentication-example).
