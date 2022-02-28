<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Controller;
use App\Exceptions\PilotIdNotFound;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect after logging in
     */
    protected mixed $redirectTo = '/dashboard';

    /** @var UserService */
    private UserService $userSvc;

    /** @var string */
    private string $loginFieldValue;

    /**
     * LoginController constructor.
     *
     * @param UserService $userSvc
     */
    public function __construct(UserService $userSvc)
    {
        $this->redirectTo = config('phpvms.login_redirect');
        $this->middleware('guest', ['except' => 'logout']);
        $this->userSvc = $userSvc;
    }

    /**
     * Get the needed authorization credentials from the request.
     * Overriding the value from the trait
     *
     * @override
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return [
            'email'    => $this->loginFieldValue,
            'password' => $request->input('password'),
        ];
    }

    /**
     * Validate the user login request.
     *
     * @override
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $id_field = $request->input('email');
        $validations = ['required', 'string'];

        /*
         * Trying to login by email or not?
         *
         * If not, run a validation rule which attempts to split the user by their VA and ID
         * Then inject that user's email into the request
         */
        if (strpos($id_field, '@') !== false) {
            $validations[] = 'email';
            $this->loginFieldValue = $request->input('email');
        } else {
            $validations[] = function ($attr, $value, $fail) use ($request) {
                try {
                    $user = $this->userSvc->findUserByPilotId($value);
                } catch (PilotIdNotFound $ex) {
                    Log::warning('Error logging in, pilot_id not found, id='.$value);
                    $fail('Pilot not found');
                    return;
                }

                $request->email = $user->email;
                $this->loginFieldValue = $user->email;
            };
        }

        $request->validate([
            'email'    => $validations,
            'password' => 'required|string',
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function sendLoginResponse(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if (setting('general.record_user_ip', true)) {
            $user->last_ip = $request->ip();
            $user->save();
        }

        if ($user->state !== UserState::ACTIVE && $user->state !== UserState::ON_LEAVE) {
            Log::info('Trying to login '.$user->ident.', state '.UserState::label($user->state));

            // Log them out
            $this->guard()->logout();
            $request->session()->invalidate();

            // Redirect to one of the error pages
            if ($user->state === UserState::PENDING) {
                return view('auth.pending');
            }

            if ($user->state === UserState::REJECTED) {
                return view('auth.rejected');
            }

            if ($user->state === UserState::SUSPENDED) {
                return view('auth.suspended');
            }
        }

        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        return redirect()->intended($this->redirectPath());
    }
}
