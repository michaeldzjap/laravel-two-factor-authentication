<?php

namespace MichaelDzjap\TwoFactorAuth\Providers;

use MichaelDzjap\TwoFactorAuth\Contracts\SMSToken;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;

class NullProvider extends BaseProvider implements TwoFactorProvider, SMSToken
{
    /**
     * {@inheritdoc}
     */
    public function register($user): void
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function unregister($user)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function verify($user, string $token)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function sendSMSToken($user): void
    {
        //
    }
}
