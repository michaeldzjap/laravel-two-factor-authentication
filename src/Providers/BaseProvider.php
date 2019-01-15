<?php

namespace MichaelDzjap\TwoFactorAuth\Providers;

abstract class BaseProvider
{
    /**
     * Check if two-factor authentication is enabled for a user.
     *
     * @param  User $user
     * @return bool
     */
    public function enabled(User $user)
    {
        return !is_null($user->twoFactorAuth);
    }
}
