<?php

namespace MichaelDzjap\TwoFactorAuth\Http\Controllers;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

trait ThrottlesTwoFactorAuths
{
    /**
     * Determine if the user has too many failed two-factor authentiction attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyTwoFactorAuthAttempts(Request $request)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), 3, 1
        );
    }

    /**
     * Increment the two-factor authentication attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function incrementTwoFactorAuthAttempts(Request $request)
    {
        $this->limiter()->hit($this->throttleKey($request));
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        $message = __('two-factor-auth.throttle', ['seconds' => $seconds]);

        $errors = [$this->fieldname() => $message];

        if ($request->expectsJson()) {
            return response()->json($errors, 423);
        }

        return redirect()->back()->withErrors($errors);
    }

    /**
     * Clear the two-factor authentication locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearTwoFactorAuthAttempts(Request $request)
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Fire an event when a lockout occurs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function fireLockoutEvent(Request $request)
    {
        event(new Lockout($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return $request->session()->get('two-factor:auth:id').'|'.$request->ip();
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }
}
