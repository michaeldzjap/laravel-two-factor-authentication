<?php

namespace MichaelDzjap\TwoFactorAuth;

use Illuminate\Support\ServiceProvider;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;
use MichaelDzjap\TwoFactorAuth\TwoFactorAuthManager;

class TwoFactorAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->publishes([
            __DIR__.'/config/twofactor-auth.php' => config_path('twofactor-auth.php'),
        ], 'config');

        $this->publishMigrations();

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'twofactor-auth');
        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang/vendor/twofactor-auth'),
        ], 'lang');

        $this->loadViewsFrom(__DIR__.'/resources/views', 'twofactor-auth');
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/twofactor-auth'),
        ], 'views');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
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

    /**
     * Publish this package's migration files.
     *
     * @return void
     */
    protected function publishMigrations(): void
    {
        $files = [
            'add_mobile_to_users_table.php',
            'create_two_factor_auths_table.php',
        ];

        $paths = [];

        foreach ($files as $file) {
            $paths[__DIR__.'/database/migrations/'.$file] = database_path('migrations/'.date('Y_m_d_His').'_'.$file);
        }

        $this->publishes($paths, 'migrations');
    }
}
