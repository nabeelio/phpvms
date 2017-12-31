<?php

namespace App\Http\Controllers\Auth;

use Log;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\RegistersUsers;
use Jackiedo\Timezonelist\Facades\Timezonelist;

use App\Models\User;
use App\Facades\Utils;
use App\Models\Airport;
use App\Models\Airline;
use App\Services\UserService;
use App\Models\Enums\UserState;
use App\Http\Controllers\Controller;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    protected $airlineRepo,
              $airportRepo,
              $userService;


    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        UserService $userService
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->userService = $userService;
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $airports = $this->airportRepo->selectBoxList();
        $airlines = $this->airlineRepo->selectBoxList();

        return $this->view('auth.register', [
            'airports' => $airports,
            'airlines' => $airlines,
            'timezones' => Timezonelist::toArray(),
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
            'airline_id' => 'required',
            'home_airport_id' => 'required',
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
            'airline_id' => $data['airline_id'],
            'home_airport_id' => $data['home_airport_id'],
            'curr_airport_id' => $data['home_airport_id'],
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
            'email' => 'required|email|unique:users,email',
            'airline_id' => 'required',
            'home_airport_id' => 'required',
            'password' => 'required|confirmed'
        ]);

        $user = $this->create($request->all());

        if($user->state === UserState::PENDING) {
            return $this->view('auth.pending');
        }

        $this->guard()->login($user);
        return redirect('/dashboard');
    }
}
