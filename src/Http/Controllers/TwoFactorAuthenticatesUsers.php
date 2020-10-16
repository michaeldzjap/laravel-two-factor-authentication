<?php

namespace MichaelDzjap\TwoFactorAuth\Http\Controllers;

use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;
use MichaelDzjap\TwoFactorAuth\Events\TwoFactorAuthenticated;
use MichaelDzjap\TwoFactorAuth\Exceptions\TokenAlreadyProcessedException;
use MichaelDzjap\TwoFactorAuth\Exceptions\TokenExpiredException;
use MichaelDzjap\TwoFactorAuth\Exceptions\TokenInvalidException;
use MichaelDzjap\TwoFactorAuth\Http\Requests\VerifySMSToken;

trait TwoFactorAuthenticatesUsers
{
    use RedirectsUsers, ThrottlesTwoFactorAuths;

    /**
     * Show the application's two-factor authentication form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showTwoFactorForm()
    {
        return view('twofactor-auth::form');
    }

    /**
     * Handle a SMS token verification request to the application.
     *
     * @param  \MichaelDzjap\TwoFactorAuth\Http\Requests\VerifySMSToken  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyToken(VerifySMSToken $request)
    {
        // If the class is using the ThrottlesTwoFactorAuths trait, we can automatically
        // throttle the two-factor authentication attempts for this application.
        // We'll key this by the username in the session storage and the IP address
        // of the client making these requests into this application.
        if ($this->hasTooManyTwoFactorAuthAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        try {
            $result = $this->attemptTwoFactorAuth($request);
        } catch (TokenInvalidException $exception) {
            $result = false;
        } catch (TokenExpiredException $exception) {
            return $this->sendKillTwoFactorAuthResponse($request);
        } catch (TokenAlreadyProcessedException $exception) {
            return $this->sendKillTwoFactorAuthResponse($request);
        }

        if ($result) {
            return $this->sendTwoFactorAuthResponse($request);
        }

        return $this->handleFailedAttempt($request);
    }

    /**
     * Attempt to pass the two-factor authentication process.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function attemptTwoFactorAuth(Request $request)
    {
        $user = config('twofactor-auth.model')::findOrFail(
            $request->session()->get('two-factor:auth')['id']
        );

        if (resolve(TwoFactorProvider::class)->verify($user, $request->input('token'))) {
            auth()->login($user);   // If SMS code validation passes, login user

            return true;
        }

        return false;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendTwoFactorAuthResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearTwoFactorAuthAttempts($request);

        $request->session()->forget('two-factor:auth');

        $user = $request->user();

        event(new TwoFactorAuthenticated($user));

        return $this->authenticated($request, $user)
            ?: redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been two-factor authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }

    /**
     * Handle the case where a user has submitted an invalid token.
     *
     * Default: If the two-factor authentication attempt was unsuccessful we
     * will increment the number of attempts to two-factor authenticate and
     * redirect the user back to the two-factor authentication form. Of course,
     * when this user surpasses their maximum number of attempts they will get
     * locked out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function handleFailedAttempt(Request $request)
    {
        $this->incrementTwoFactorAuthAttempts($request);

        if ($path = $this->redirectAfterFailurePath()) {
            return redirect()->to($path)->withErrors([
                'token' => __('twofactor-auth::twofactor-auth.failed'),
            ]);
        }

        return $this->sendFailedTwoFactorAuthResponse($request);
    }

    /**
     * Get the post two-factor authentication failure redirect path.
     *
     * @return null|string
     */
    protected function redirectAfterFailurePath(): ?string
    {
        if (method_exists($this, 'redirectToAfterFailure')) {
            return $this->redirectToAfterFailure();
        }

        if (property_exists($this, 'redirectToAfterFailure')) {
            return $this->redirectToAfterFailure;
        }

        return null;
    }

    /**
     * Throw a validation exception when two-factor authentication attempt fails.
     * NOTE: Throwing a validation exception is cleaner than redirecting, but
     * we can only do it here because we don't need to redirect to the login route.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedTwoFactorAuthResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'token' => [__('twofactor-auth::twofactor-auth.failed')],
        ]);
    }

    /**
     * Get the kill two-factor authentication response instance in case a token
     * is either expired or already processed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendKillTwoFactorAuthResponse(Request $request)
    {
        $errors = ['token' => __('twofactor-auth::twofactor-auth.expired')];

        if ($request->expectsJson()) {
            return response()->json($errors, 401);
        }

        return redirect()->to('/login')->withErrors($errors);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username(): string
    {
        return 'email';
    }
}
