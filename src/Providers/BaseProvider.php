<?php

namespace MichaelDzjap\TwoFactorAuth\Providers;

use App\User;

abstract class BaseProvider
{
    /**
     * Check if two-factor authentication is enabled, dependent on the "enabled" config option.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function enabled(User $user)
    {
        $conf = config('twofactor-auth.enabled', 'per_user');
        if ($conf === 'per_user') {
            return !is_null($user->twoFactorAuth);
        }
        return $conf === 'always';
    }
}
