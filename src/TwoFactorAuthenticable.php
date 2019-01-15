<?php

namespace MichaelDzjap\TwoFactorAuth;

use Illuminate\Database\Eloquent\Relations\HasOne;
use MichaelDzjap\TwoFactorAuth\TwoFactorAuth;

trait TwoFactorAuthenticable
{
    /**
     * Get the two-factor auth record associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function twoFactorAuth() : HasOne
    {
        return $this->hasOne(TwoFactorAuth::class);
    }

    /**
     * Set the two-factor auth id.
     *
     * @param  string $id
     * @return void
     */
    public function setTwoFactorAuthId(string $id) : void
    {
        $this->twoFactorAuth->update(['id' => $id]);
    }

    /**
     * Get the two-factor auth id.
     *
     * @return string $id
     */
    public function getTwoFactorAuthId() : string
    {
        return $this->twoFactorAuth->id;
    }
}
