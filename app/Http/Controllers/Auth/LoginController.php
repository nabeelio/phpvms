<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Models\Enums\PilotState;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/dashboard';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showLoginForm()
    {
        return $this->view('auth/login');
    }

    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        $user = Auth::user();

        // TODO: How to handle ON_LEAVE?
        if($user->state !== PilotState::ACTIVE) {

            // Log them out
            $this->guard()->logout();
            $request->session()->invalidate();

            // Redirect to one of the error pages
            if($user->state === PilotState::PENDING) {
                return $this->view('auth.pending');
            }

            elseif ($user->state === PilotState::REJECTED) {
                return $this->view('auth.rejected');
            }
        }

        return $this->authenticated($request, $this->guard()->user())
            ?: redirect()->intended($this->redirectPath());
    }
}
