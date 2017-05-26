<?php

namespace MichaelDzjap\TwoFactorAuth;

use Illuminate\Support\ServiceProvider;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;
use MichaelDzjap\TwoFactorAuth\TwoFactorAuthManager;

class TwoFactorAuthServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'twofactor-auth');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'twofactor-auth');

        $this->publishes([
            __DIR__.'/config/twofactor-auth.php' => config_path('twofactor-auth.php'),
        ]);

        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang/vendor/twofactor-auth'),
        ], 'lang');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/twofactor-auth'),
        ], 'views');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/twofactor-auth.php', 'twofactor-auth'
        );

        $this->app->singleton(TwoFactorAuthManager::class, function ($app) {
            return new TwoFactorAuthManager($app);
        });

        $this->app->singleton(TwoFactorProvider::class, function ($app) {
            return $app->make(TwoFactorAuthManager::class)->provider();
        });
    }
}
