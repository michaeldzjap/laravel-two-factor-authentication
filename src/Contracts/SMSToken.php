<?php

namespace MichaelDzjap\TwoFactorAuth\Contracts;

use App\User;

interface SMSToken
{
    /**
     * Send a user a two-factor authentication token via SMS.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function sendSMSToken(User $user) : void;
}
