<?php

namespace MichaelDzjap\TwoFactorAuth\Providers;

abstract class BaseProvider
{
    /**
     * Check if two-factor authentication is enabled.
     *
     * @param  mixed  $user
     * @return bool
     */
    public function enabled($user)
    {
        $enabled = config('twofactor-auth.enabled', 'user');

        if ($enabled === 'user') {
            return ! is_null($user->twoFactorAuth);
        }

        return $enabled === 'always';
    }
}
