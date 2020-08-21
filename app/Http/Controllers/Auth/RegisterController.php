<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\Controller;
use App\Models\Enums\UserState;
use App\Models\User;
use App\Models\UserField;
use App\Models\UserFieldValue;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Services\UserService;
use App\Support\Countries;
use App\Support\Timezonelist;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

        $this->redirectTo = config('phpvms.registration_redirect');
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function showRegistrationForm()
    {
        $airports = $this->airportRepo->selectBoxList(false, setting('pilots.home_hubs_only'));
        $airlines = $this->airlineRepo->selectBoxList();
        $userFields = UserField::where(['show_on_registration' => true])->get();

        return view('auth.register', [
            'airports'   => $airports,
            'airlines'   => $airlines,
            'countries'  => Countries::getSelectList(),
            'timezones'  => Timezonelist::toArray(),
            'userFields' => $userFields,
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
            'email'           => 'required|email|max:255|unique:users,email',
            'airline_id'      => 'required',
            'home_airport_id' => 'required',
            'password'        => 'required|min:5|confirmed',
            'toc_accepted'    => 'accepted',
        ];

        // Dynamically add the required fields
        $userFields = UserField::where([
            'show_on_registration' => true,
            'required'             => true,
            'active'               => true,
        ])->get();

        foreach ($userFields as $field) {
            $rules['field_'.$field->slug] = 'required';
        }

        if (config('captcha.enabled')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        return Validator::make($data, $rules);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $opts
     *
     * @throws \RuntimeException
     * @throws \Exception
     *
     * @return User
     */
    protected function create(array $opts)
    {
        // Default options
        $opts['password'] = Hash::make($opts['password']);

        // Convert transfer hours into minutes
        if (isset($opts['transfer_time'])) {
            $opts['transfer_time'] *= 60;
        }

        $user = $this->userService->createUser($opts);

        Log::info('User registered: ', $user->toArray());

        $userFields = UserField::all();
        foreach ($userFields as $field) {
            $field_name = 'field_'.$field->slug;
            UserFieldValue::updateOrCreate([
                'user_field_id' => $field->id,
                'user_id'       => $user->id,
            ], ['value' => $opts[$field_name]]);
        }

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
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());
        if ($user->state === UserState::PENDING) {
            return view('auth.pending');
        }

        $this->guard()->login($user);

        return redirect('/dashboard');
    }
}
