<?php

namespace MichaelDzjap\TwoFactorAuth;

use InvalidArgumentException;
use MessageBird\Client;
use MichaelDzjap\TwoFactorAuth\Providers\MessageBirdVerify;
use MichaelDzjap\TwoFactorAuth\Providers\NullProvider;

class TwoFactorAuthManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * The array of resolved two-factor authentication drivers.
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function provider($driver = null)
    {
        return $this->driver($driver);
    }

    /**
     * Get a driver instance.
     *
     * @param  string  $name
     * @return mixed
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return $this->drivers[$name] = $this->get($name);
    }

    /**
     * Attempt to get the connection from the local cache.
     *
     * @param  string  $name
     * @return \MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider
     */
    protected function get($name)
    {
        return isset($this->drivers[$name]) ? $this->drivers[$name] : $this->resolve($name);
    }

    /**
     * Resolve the given store.
     *
     * @param  string  $name
     * @return \MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("TwoFactorProvider [{$name}] is not defined.");
        }

        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';

        if (! method_exists($this, $driverMethod)) {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }

        return $this->{$driverMethod}($config);
    }

    /**
     * Create an instance of the driver.
     *
     * @param  array  $config
     * @return \MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider
     */
    protected function createMessageBirdDriver(array $config)
    {
        return new MessageBirdVerify(
            new Client($config['key'])
        );
    }

    /**
     * Create an instance of the driver.
     *
     * @param  array  $config
     * @return \MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider
     */
    protected function createNullDriver(array $config)
    {
        return new NullProvider;
    }

    /**
     * Get the provider configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["twofactor-auth.providers.{$name}"];
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['twofactor-auth.default'];
    }

    /**
     * Set the default driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['twofactor-auth.default'] = $name;
    }
}
