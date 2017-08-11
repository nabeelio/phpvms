<?php

namespace App\Http\Controllers\Auth;

use Validator;
use App\Models\Role;
use App\Models\User;
use App\Models\Airport;
use App\Models\Airline;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
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

    public function register()
    {
        // Validate
        $this->validate(request(), [
            'name' => 'required',
            'email' => 'required|email',
            'airline' => 'required',
            'home_airport' => 'required',
            'password' => 'required|confirmed'
        ]);

        # TODO: I'm just feeling we need to do something with Ranking? I forgot.
        $user = User::create(['name' => request('name'),
                              'email' => request('email'),
                              'airline_id' => request('airline'),
                              'home_airport_id' => request('home_airport'),
                              'curr_airport_id' => request('home_airport'),
                              'password' => Hash::make(request('password')),
                              'rank_id' => 1]);
        //Attach the user roles
        $role = Role::where('name', 'user')->first();
        $user->attachRole($role);
        return $this->view('auth.registered');
    }
}
