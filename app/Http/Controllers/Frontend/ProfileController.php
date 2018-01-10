<?php

namespace App\Http\Controllers\Frontend;

use Log;
use Hash;
use Flash;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jackiedo\Timezonelist\Facades\Timezonelist;

use App\Models\User;
use App\Facades\Utils;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;

class ProfileController extends AppBaseController
{
    private $airlineRepo,
            $airportRepo,
            $userRepo;

    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        UserRepository $userRepo
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->userRepo = $userRepo;
    }

    public function index()
    {
        $airports = $this->airportRepo->all();

        return $this->view('profile.index', [
            'user' => Auth::user(),
            'airports' => $airports,
        ]);
    }

    public function show($id)
    {
        $user = User::where('id', $id)->first();
        if (empty($user)) {
            Flash::error('User not found!');
            return redirect(route('frontend.dashboard.index'));
        }

        $airports = $this->airportRepo->all();

        return $this->view('profile.index', [
            'user' => $user,
            'airports' => $airports,
        ]);
    }

    /**
     * Show the edit for form the user's profile
     */
    public function edit(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        if (empty($user)) {
            Flash::error('User not found!');
            return redirect(route('frontend.dashboard.index'));
        }

        $airlines = $this->airlineRepo->selectBoxList();
        $airports = $this->airportRepo->selectBoxList();

        $countries = collect((new \League\ISO3166\ISO3166)->all())
            ->mapWithKeys(function($item, $key) {
                return [strtolower($item['alpha2']) => $item['name']];
            });

        return $this->view('profile.edit', [
            'user'      => $user,
            'airlines'  => $airlines,
            'airports'  => $airports,
            'countries' => $countries,
            'timezones' => Timezonelist::toArray(),
        ]);
    }

    public function update(Request $request)
    {
        $id = Auth::user()->id;
        $user = $this->userRepo->findWithoutFail($id);

        $validator = Validator::make($request->toArray(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'airline_id' => 'required',
            'password' => 'confirmed'
        ]);

        if ($validator->fails()) {
            Log::info('validator failed for user '.$user->pilot_id);
            Log::info($validator->errors()->toArray());

            return redirect(route('frontend.profile.edit', $id))
                ->withErrors($validator)
                ->withInput();
        }

        $req_data = $request->all();
        if (!$request->filled('password')) {
            unset($req_data['password']);
        } else {
            $req_data['password'] = Hash::make($req_data['password']);
        }

        $this->userRepo->update($req_data, $id);

        Flash::success('Profile updated successfully!');
        return redirect(route('frontend.profile.index'));
    }

    /**
     * Regenerate the user's API key
     */
    public function regen_apikey(Request $request)
    {
        $user = User::find(Auth::user()->id);
        Log::info('Regenerating API key "'.$user->pilot_id.'"');

        $user->api_key = Utils::generateApiKey();
        $user->save();

        flash('New API key generated!')->success();
        return redirect(route('frontend.profile.index'));
    }
}
