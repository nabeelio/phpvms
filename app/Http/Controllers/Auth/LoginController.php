<?php

namespace App\Http\Controllers\Auth;

use App\Interfaces\Controller;
use App\Models\Enums\UserState;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Class LoginController
 * @package App\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    /**
     * LoginController constructor.
     */
    public function __construct()
    {
        $this->redirectTo = config('app.login_redirect');
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth/login');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function sendLoginResponse(Request $request)
    {
        $user = Auth::user();

        $user->last_ip = $request->ip();
        $user->save();

        // TODO: How to handle ON_LEAVE?
        if ($user->state !== UserState::ACTIVE) {
            Log::info('Trying to login '.$user->pilot_id.', state '
                .UserState::label($user->state));

            // Log them out
            $this->guard()->logout();
            $request->session()->invalidate();

            // Redirect to one of the error pages
            if ($user->state === UserState::PENDING) {
                return view('auth.pending');
            } elseif ($user->state === UserState::REJECTED) {
                return view('auth.rejected');
            } elseif ($user->state === UserState::SUSPENDED) {
                return view('auth.suspended');
            }
        }

        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        return redirect()->intended($this->redirectPath());
    }
}
