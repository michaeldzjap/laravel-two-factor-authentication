<?php

namespace MichaelDzjap\TwoFactorAuth\Contracts;

interface TwoFactorProvider
{
    /**
     * Check if two-factor authentication is enabled for a user.
     *
     * @param  mixed  $user
     * @return bool
     */
    public function enabled($user);

    /**
     * Register a user with this provider.
     *
     * @param  mixed  $user
     * @return void
     */
    public function register($user): void;

    /**
     * Unregister a user with this provider.
     *
     * @param  mixed  $user
     * @return bool
     */
    public function unregister($user);

    /**
     * Determine if the token is valid.
     *
     * @param  mixed  $user
     * @param  string  $token
     * @return bool
     */
    public function verify($user, string $token);
}
