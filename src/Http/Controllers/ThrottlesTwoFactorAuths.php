<?php

namespace MichaelDzjap\TwoFactorAuth\Http\Controllers;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait ThrottlesTwoFactorAuths
{
    use ThrottlesLogins;

    /**
     * Determine if the user has too many failed two-factor authentiction attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function hasTooManyTwoFactorAuthAttempts(Request $request)
    {
        return self::hasTooManyLoginAttempts($request);
    }

    /**
     * Increment the two-factor authentication attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function incrementTwoFactorAuthAttempts(Request $request): void
    {
        self::incrementLoginAttempts($request);
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

        $message = __('twofactor-auth::twofactor-auth.throttle', ['seconds' => $seconds]);

        $errors = ['token' => $message];

        if ($request->expectsJson()) {
            return response()->json($errors, 429);
        }

        return redirect()->to('/login')
            ->withInput(
                Arr::only($request->session()->get('two-factor:auth'), [$this->username(), 'remember'])
            )
            ->withErrors($errors);
    }

    /**
     * Clear the two-factor authentication locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearTwoFactorAuthAttempts(Request $request): void
    {
        self::clearLoginAttempts($request);
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->session()->get('two-factor:auth')[$this->username()]).'|'.$request->ip();
    }
}
