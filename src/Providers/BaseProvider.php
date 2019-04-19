<?php

namespace MichaelDzjap\TwoFactorAuth\Providers;

use App\User;

abstract class BaseProvider
{
    /**
     * Check if two-factor authentication is enabled.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function enabled(User $user)
    {
        $enabled = config('twofactor-auth.enabled', 'user');

        if ($enabled === 'user') {
            return !is_null($user->twoFactorAuth);
        }

        return $enabled === 'always';
    }
}
