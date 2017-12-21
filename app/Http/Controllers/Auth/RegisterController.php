<?php

namespace App\Http\Controllers\Auth;

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
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function create(array $data)
    {
        # First, validate the posted data
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|email',
            'airline' => 'required',
            'home_airport' => 'required',
            'password' => 'required|confirmed'
        ]);

        # Let's tell the service to create the pilot
        if($p = $this->userService->createPilot($data))
        {
            //return $this->view('auth.registered');
            return $p;
        }

        # I'm not sure if we really need to add the error something if createPilot fails?
    }
}
