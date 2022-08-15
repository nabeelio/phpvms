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
use App\Support\HttpClient;
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

    private AirlineRepository $airlineRepo;
    private AirportRepository $airportRepo;
    private HttpClient $httpClient;
    private UserService $userService;

    /**
     * RegisterController constructor.
     *
     * @param AirlineRepository $airlineRepo
     * @param AirportRepository $airportRepo
     * @param UserService       $userService
     * @param HttpClient        $httpClient
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        HttpClient $httpClient,
        UserService $userService,
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->httpClient = $httpClient;
        $this->userService = $userService;

        $this->middleware('guest');

        $this->redirectTo = config('phpvms.registration_redirect');
        $this->httpClient = $httpClient;
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
        $userFields = UserField::where(['show_on_registration' => true, 'active' => true])->get();

        return view('auth.register', [
            'airports'   => $airports,
            'airlines'   => $airlines,
            'countries'  => Countries::getSelectList(),
            'timezones'  => Timezonelist::toArray(),
            'userFields' => $userFields,
            'captcha'    => [
                'enabled'    => setting('captcha.enabled', env('CAPTCHA_ENABLED', false)),
                'site_key'   => setting('captcha.site_key', env('CAPTCHA_SITE_KEY')),
                'secret_key' => setting('captcha.secret_key', env('CAPTCHA_SECRET_KEY')),
            ],
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

        /*
         * Validation for hcaptcha
         */
        $captcha_enabled = setting('captcha.enabled', env('CAPTCHA_ENABLED', false));
        if ($captcha_enabled === true) {
            $rules['h-captcha-response'] = [
                'required',
                function ($attribute, $value, $fail) {
                    $response = $this->httpClient->form_post('https://hcaptcha.com/siteverify', [
                        'secret'   => setting('captcha.secret_key', env('CAPTCHA_SECRET_KEY')),
                        'response' => $value,
                    ]);

                    if ($response['success'] !== true) {
                        Log::error('Captcha failed '.json_encode($response));
                        $fail('Captcha verification failed, please try again.');
                    }
                },
            ];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $opts
     *
     * @throws \Exception
     * @throws \RuntimeException
     *
     * @return User
     */
    protected function create(Request $request): User
    {
        // Default options
        $opts = $request->all();
        $opts['password'] = Hash::make($opts['password']);

        if (setting('general.record_user_ip', true)) {
            $opts['last_ip'] = $request->ip();
        }

        // Convert transfer hours into minutes
        if (isset($opts['transfer_time'])) {
            $opts['transfer_time'] *= 60;
        }

        $user = $this->userService->createUser($opts);

        Log::info('User registered: ', $user->toArray());

        $userFields = UserField::where(['show_on_registration' => true, 'active' => true])->get();
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

        $user = $this->create($request);
        if ($user->state === UserState::PENDING) {
            return view('auth.pending');
        }

        $this->guard()->login($user);

        return redirect('/dashboard');
    }
}
