<?php

namespace MichaelDzjap\TwoFactorAuth;

use Illuminate\Support\Manager;
use InvalidArgumentException;
use MessageBird\Client;
use MichaelDzjap\TwoFactorAuth\Providers\MessageBirdVerify;
use MichaelDzjap\TwoFactorAuth\Providers\NullProvider;

class TwoFactorAuthManager extends Manager
{
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
     * Create an instance of the driver.
     *
     * @return \MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider
     */
    protected function createMessageBirdDriver()
    {
        return new MessageBirdVerify(
            new Client($this->app['config']["twofactor-auth.providers.messagebird.key"])
        );
    }

    /**
     * Create an instance of the driver.
     *
     * @return \MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider
     */
    protected function createNullDriver()
    {
        return new NullProvider;
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
