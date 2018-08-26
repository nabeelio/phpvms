<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Utils;
use App\Interfaces\Controller;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Services\UserService;
use App\Support\Countries;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Jackiedo\Timezonelist\Facades\Timezonelist;
use Log;
use Validator;

/**
 * Class RegisterController
 */
class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    private $airlineRepo;
    private $airportRepo;
    private $userService;

    /**
     * RegisterController constructor.
     *
     * @param AirlineRepository $airlineRepo
     * @param AirportRepository $airportRepo
     * @param UserService       $userService
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        UserService $userService
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->userService = $userService;
        $this->middleware('guest');

        $this->redirectTo = config('app.registration_redirect');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $airports = $this->airportRepo->selectBoxList(false, true);
        $airlines = $this->airlineRepo->selectBoxList();

        return view('auth.register', [
            'airports'  => $airports,
            'airlines'  => $airlines,
            'countries' => Countries::getSelectList(),
            'timezones' => Timezonelist::toArray(),
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $rules = [
            'name'            => 'required|max:255',
            'email'           => 'required|email|max:255|unique:users',
            'airline_id'      => 'required',
            'home_airport_id' => 'required',
            'password'        => 'required|min:5|confirmed',
        ];

        if (config('captcha.enabled')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        return Validator::make($data, $rules);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @throws \RuntimeException
     * @throws \Exception
     *
     * @return User
     */
    protected function create(array $data)
    {
        // Default options
        $opts = array_merge([
            'api_key' => Utils::generateApiKey(),
        ], $data);

        $opts['curr_airport_id'] = $data['home_airport_id'];
        $opts['password'] = Hash::make($data['password']);

        $user = User::create($opts);
        $user = $this->userService->createPilot($user);

        Log::info('User registered: ', $user->toArray());

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param Request $request
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function register(Request $request)
    {
        $rules = [
            'name'            => 'required',
            'email'           => 'required|email|unique:users,email',
            'airline_id'      => 'required',
            'home_airport_id' => 'required',
            'password'        => 'required|confirmed',
            'timezone'        => 'required',
            'country'         => 'required',
        ];

        if (config('captcha.enabled')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $this->validate(request(), $rules);

        $user = $this->create($request->all());
        if ($user->state === UserState::PENDING) {
            return view('auth.pending');
        }

        $this->guard()->login($user);

        return redirect('/dashboard');
    }
}
