# Two-Factor-Authentication
A two-factor authentication package for Laravel 5.4

## Description
This is a two-factor authentication package for *Laravel 5.4*. It is heavily inspired by the [Laravel Two-Factor Authentication](https://github.com/srmklive/laravel-twofactor-authentication) package. The main differences between this package and the aforementioned package are:

- This package is tuned to work with the *MessageBird Verify* api instead of *Authy* out of the box. It is possible to implement your own custom two-factor authentication provider quite easily though.
- This package uses throttling to limit the number of unsuccessful authentication attempts in a certain amount of time.
- This package is only guaranteed to work with Laravel 5.4. Prior version have not been tested.

## Installation
To install using *Composer* run:
```
composer require michaeldzjap/twofactor-auth
```
Add the service provider to the `'providers'` array in `config/app.php`:
```
MichaelDzjap\TwoFactorAuth\TwoFactorAuthServiceProvider::class
```
Run the following *artisan* command to publish the configuration, language and view files:
```
php artisan vendor:publish
```
If you only want to publish only one of these file groups, for instance if you don't need the views or language files, you can append one of the following commands to the *artisan* command: `--tag=config`, `--tag=lang` or `--tag-views`.

Run the following *artisan* command to run the database migrations
```
php artisan migrate
```
This will add a `mobile` column to the `users` table and create a `two_factor_auths` table.

Add the following trait to your `User` model:
```
use MichaelDzjap\TwoFactorAuth\TwoFactorAuthenticable;
...

```
