<?php

namespace App\Http\Controllers\Auth;

use App\Models\Enums\PilotState;
use Log;
use App\Facades\Utils;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\Airport;
use App\Models\Airline;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $userService;


    public function __construct(
        UserService $userService
    ) {
        $this->userService = $userService;
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $airports = Airport::all();
        $airlines = Airline::all();
        return $this->view('auth.register', [
            'airports' => $airports,
            'airlines' => $airlines,
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'airline' => 'required',
            'home_airport' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws \RuntimeException
     */
    protected function create(array $data)
    {
        $opts = [
            'name' => $data['name'],
            'email' => $data['email'],
            'api_key' => Utils::generateApiKey(),
            'airline_id' => $data['airline'],
            'home_airport_id' => $data['home_airport'],
            'curr_airport_id' => $data['home_airport'],
            'password' => Hash::make($data['password'])
        ];

        $user = User::create($opts);
        $user = $this->userService->createPilot($user);

        Log::info('User registered: ', $user->toArray());

        return $user;
    }

    /**
     * Handle a registration request for the application.
     * @throws \RuntimeException
     */
    public function register(Request $request)
    {
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'airline' => 'required',
            'home_airport' => 'required',
            'password' => 'required|confirmed'
        ]);

        $user = $this->create($request->all());

        if($user->state === PilotState::PENDING) {
            return $this->view('auth.pending');
        }

        $this->guard()->login($user);
        return redirect('/dashboard');
    }
}
