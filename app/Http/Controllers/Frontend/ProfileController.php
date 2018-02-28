<?php

namespace App\Http\Controllers\Frontend;

use App\Facades\Utils;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\UserRepository;
use App\Support\Countries;
use Flash;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jackiedo\Timezonelist\Facades\Timezonelist;
use Log;
use Validator;

class ProfileController extends Controller
{
    private $airlineRepo,
            $airportRepo,
            $userRepo;

    /**
     * ProfileController constructor.
     * @param AirlineRepository $airlineRepo
     * @param AirportRepository $airportRepo
     * @param UserRepository $userRepo
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        UserRepository $userRepo
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if(setting('pilots.home_hubs_only')) {
            $airports = $this->airportRepo->findWhere(['hub' => true]);
        } else {
            $airports = $this->airportRepo->all();
        }

        return $this->view('profile.index', [
            'user' => Auth::user(),
            'airports' => $airports,
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        if (empty($user)) {
            Flash::error('User not found!');
            return redirect(route('frontend.dashboard.index'));
        }

        $airlines = $this->airlineRepo->selectBoxList();
        $airports = $this->airportRepo->selectBoxList(false, setting('pilots.home_hubs_only'));

        return $this->view('profile.edit', [
            'user'      => $user,
            'airlines'  => $airlines,
            'airports'  => $airports,
            'countries' => Countries::getSelectList(),
            'timezones' => Timezonelist::toArray(),
        ]);
    }

    /**
     * @param Request $request
     * @return $this
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
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
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
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
