<?php

namespace MichaelDzjap\TwoFactorAuth;

use Illuminate\Support\Manager;
use MessageBird\Client;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;
use MichaelDzjap\TwoFactorAuth\Providers\MessageBirdVerify;
use MichaelDzjap\TwoFactorAuth\Providers\NullProvider;

class TwoFactorAuthManager extends Manager
{
    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return \MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider
     */
    public function provider(string $driver = null): TwoFactorProvider
    {
        return $this->driver($driver);
    }

    /**
     * Create an instance of the driver.
     *
     * @return \MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider
     */
    protected function createMessageBirdDriver(): TwoFactorProvider
    {
        return new MessageBirdVerify(
            new Client($this->config['twofactor-auth.providers.messagebird.key'])
        );
    }

    /**
     * Create an instance of the driver.
     *
     * @return \MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider
     */
    protected function createNullDriver(): TwoFactorProvider
    {
        return new NullProvider;
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->config['twofactor-auth.default'];
    }

    /**
     * Set the default driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver(string $name): void
    {
        $this->config['twofactor-auth.default'] = $name;
    }
}
