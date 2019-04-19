<?php

namespace MichaelDzjap\TwoFactorAuth\Contracts;

use App\User;

interface TwoFactorProvider
{
    /**
     * Check if two-factor authentication is enabled for a user.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function enabled(User $user);

    /**
     * Register a user with this provider.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function register(User $user) : void;

    /**
     * Unregister a user with this provider.
     *
     * @param  \App\User  $user
     * @return bool
     */
    public function unregister(User $user);

    /**
     * Determine if the token is valid.
     *
     * @param  \App\User  $user
     * @param  string  $token
     * @return bool
     */
    public function verify(User $user, string $token);
}
