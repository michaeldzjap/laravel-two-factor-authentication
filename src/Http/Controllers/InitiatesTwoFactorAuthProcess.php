<?php

namespace MichaelDzjap\TwoFactorAuth\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;

trait InitiatesTwoFactorAuthProcess
{
    /**
     * Determine if the two-factor authentication process should be started for this user.
     *
     * @param  Request $request
     * @param  User $user
     */
    private function shouldTwoFactorAuthenticate(Request $request, User $user)
    {
        if (resolve(TwoFactorProvider::class)->enabled($user)) {
            return self::startTwoFactorAuthProcess($request, $user);
        }

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Log out the user and start the two factor authentication state.
     *
     * @param  Request $request
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    private function startTwoFactorAuthProcess(Request $request, $user)
    {
        // Logout user, but remember user id
        auth()->logout();
        $request->session()->put('two-factor:auth:id', $user->id);

        self::registerUserAndSendToken($user);

        return redirect(route('auth.token'))->send();
    }
}
