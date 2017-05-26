<?php

namespace MichaelDzjap\TwoFactorAuth\Providers;

use App\User;
use MichaelDzjap\TwoFactorAuth\Contracts\SMSToken;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;

class NullProvider implements TwoFactorProvider, SMSToken
{
    /**
     * {@inheritdoc}
     */
    public function enabled(User $user)
    {
        return $user->twoFactorAuth;
    }

    /**
     * {@inheritdoc}
     */
    public function register(User $user)
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
