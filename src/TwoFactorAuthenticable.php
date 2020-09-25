<?php

namespace MichaelDzjap\TwoFactorAuth;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

trait TwoFactorAuthenticable
{
    /**
     * Get the mobile phone number associated with the user.
     *
     * Override in your User model to suit your application.
     *
     * @return string
     */
    public function getMobile(): string
    {
        return $this->mobile;
    }

    /**
     * Get the two-factor auth record associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function twoFactorAuth(): HasOne
    {
        return $this->hasOne(
            \MichaelDzjap\TwoFactorAuth\Models\TwoFactorAuth::class, 'user_id', $this->getKeyName()
        );
    }

    /**
     * Set the two-factor auth id.
     *
     * @param  string  $id
     * @return void
     */
    public function setTwoFactorAuthId(string $id): void
    {
        $enabled = config('twofactor-auth.enabled', 'user');

        if ($enabled === 'user') {
            $this->twoFactorAuth->update(['id' => $id]);
        }

        if ($enabled === 'always') {
            $this->upsertTwoFactorAuthId($id);
        }
    }

    /**
     * Get the two-factor auth id.
     *
     * @return string
     */
    public function getTwoFactorAuthId(): string
    {
        return $this->twoFactorAuth->id;
    }

    /**
     * Create or update a two-factor authentication record with the given id.
     *
     * @param  string  $id
     * @return void
     */
    private function upsertTwoFactorAuthId(string $id): void
    {
        DB::transaction(function () use ($id) {
            $attributes = ['id' => $id];

            if (! $this->twoFactorAuth()->exists()) {
                $this->twoFactorAuth()->create($attributes);
            } else {
                $this->twoFactorAuth->update($attributes);
            }
        });
    }
}
