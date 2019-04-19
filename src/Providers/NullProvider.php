<?php

namespace MichaelDzjap\TwoFactorAuth\Providers;

use App\User;
use MichaelDzjap\TwoFactorAuth\Contracts\SMSToken;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;

class NullProvider extends BaseProvider implements TwoFactorProvider, SMSToken
{
    /**
     * {@inheritdoc}
     */
    public function register(User $user) : void
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function unregister(User $user)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function verify(User $user, string $token)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function sendSMSToken(User $user)
    {
        //
    }
}
