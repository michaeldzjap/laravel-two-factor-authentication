<?php

namespace MichaelDzjap\TwoFactorAuth\Http\Controllers;

use App\User;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Http\Request;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;
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
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the two-factor authentication attempts for this application. We'll key this by
        // the user id in the session storage and the IP address of the client making
        // these requests into this application.
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

        // If the two-factor authentication attempt was unsuccessful we will increment
        // the number of attempts to two-factor authenticate and redirect the user
        // back to the two-factor authentication form. Of course, when this user
        // surpasses their maximum number of attempts they will get locked out.
        $this->incrementTwoFactorAuthAttempts($request);

        return $this->sendFailedTwoFactorAuthResponse($request);
    }

    /**
     * Attempt to pass the two-factor authentication process.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function attemptTwoFactorAuth(Request $request)
    {
        $user = User::findOrFail($request->session()->get('two-factor:auth:id'));

        if (resolve(TwoFactorProvider::class)->verify($user, $request->input('token'))) {
            $request->session()->forget('two-factor:auth:id');
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

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Get the failed two-factor authentication response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedTwoFactorAuthResponse(Request $request)
    {
        $errors = [$this->fieldname() => __('two-factor-auth.failed')];

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()->withErrors($errors);
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
        $errors = [$this->fieldname() => __('two-factor-auth.expired')];

        if ($request->expectsJson()) {
            return response()->json($errors, 401);
        }

        return redirect()->back()->withErrors($errors);
    }

    /**
     * Get the input field identifier to be used by the controller.
     *
     * @return string
     */
    public function fieldname()
    {
        return 'token';
    }
}
