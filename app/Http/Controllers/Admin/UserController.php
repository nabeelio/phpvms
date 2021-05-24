<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Rank;
use App\Models\User;
use App\Models\UserAward;
use App\Repositories\AirlineRepository;
use App\Repositories\AirportRepository;
use App\Repositories\PirepRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Services\UserService;
use App\Support\Timezonelist;
use App\Support\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use League\ISO3166\ISO3166;
use Prettus\Repository\Exceptions\RepositoryException;

class UserController extends Controller
{
    private $airlineRepo;
    private $airportRepo;
    private $pirepRepo;
    private $roleRepo;
    private $userRepo;
    private $userSvc;

    /**
     * UserController constructor.
     *
     * @param AirlineRepository $airlineRepo
     * @param AirportRepository $airportRepo
     * @param PirepRepository   $pirepRepo
     * @param RoleRepository    $roleRepo
     * @param UserRepository    $userRepo
     * @param UserService       $userSvc
     */
    public function __construct(
        AirlineRepository $airlineRepo,
        AirportRepository $airportRepo,
        PirepRepository $pirepRepo,
        RoleRepository $roleRepo,
        UserRepository $userRepo,
        UserService $userSvc
    ) {
        $this->airlineRepo = $airlineRepo;
        $this->airportRepo = $airportRepo;
        $this->pirepRepo = $pirepRepo;
        $this->roleRepo = $roleRepo;
        $this->userSvc = $userSvc;
        $this->userRepo = $userRepo;
    }

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            $users = $this->userRepo->searchCriteria($request, false)
                ->orderBy('created_at', 'desc')
                ->paginate();
        } catch (RepositoryException $e) {
        }

        return view('admin.users.index', [
            'users'   => $users,
            'country' => new ISO3166(),
        ]);
    }

    /**
     * Show the form for creating a new User.
     *
     * @return mixed
     */
    public function create()
    {
        $airlines = $this->airlineRepo->selectBoxList();
        $airports = $this->airportRepo->selectBoxList(false);
        $countries = collect((new ISO3166())->all())
            ->mapWithKeys(function ($item, $key) {
                return [strtolower($item['alpha2']) => $item['name']];
            });
        $roles = $this->roleRepo->selectBoxList(false, true);

        return view('admin.users.create', [
            'user'      => null,
            'pireps'    => null,
            'airlines'  => $airlines,
            'timezones' => Timezonelist::toArray(),
            'country'   => new ISO3166(),
            'countries' => $countries,
            'airports'  => $airports,
            'ranks'     => Rank::all()->pluck('name', 'id'),
            'roles'     => $roles,
        ]);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();
        $user = $this->userRepo->create($input);

        Flash::success('User saved successfully.');
        return redirect(route('admin.users.index'));
    }

    /**
     * Display the specified User.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        return $this->edit($id);
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param int $id
     *
     * @throws RepositoryException
     *
     * @return mixed
     */
    public function edit($id)
    {
        $user = $this->userRepo
            ->with(['awards', 'fields', 'rank'])
            ->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }

        $pireps = $this->pirepRepo
            ->whereOrder(['user_id' => $id], 'created_at', 'desc')
            ->paginate();

        $countries = collect((new ISO3166())->all())
            ->mapWithKeys(function ($item, $key) {
                return [strtolower($item['alpha2']) => $item['name']];
            });

        $airlines = $this->airlineRepo->selectBoxList();
        $airports = $this->airportRepo->selectBoxList(false);
        $roles = $this->roleRepo->selectBoxList(false, true);

        return view('admin.users.edit', [
            'user'      => $user,
            'pireps'    => $pireps,
            'country'   => new ISO3166(),
            'countries' => $countries,
            'timezones' => Timezonelist::toArray(),
            'airports'  => $airports,
            'airlines'  => $airlines,
            'ranks'     => Rank::all()->pluck('name', 'id'),
            'roles'     => $roles,
        ]);
    }

    /**
     * Update the specified User in storage.
     *
     * @param int               $id
     * @param UpdateUserRequest $request
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return mixed
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userRepo->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('admin.users.index'));
        }

        $req_data = $request->all();
        if (!$request->filled('password')) {
            unset($req_data['password']);
        } else {
            $req_data['password'] = Hash::make($req_data['password']);
        }

        if ($request->filled('avatar_upload')) {
            /**
             * @var $file \Illuminate\Http\UploadedFile
             */
            $file = $request->file('avatar_upload');
            $file_path = $file->storeAs(
                'avatars',
                str_slug($file->getClientOriginalName()),
                config('filesystems.public_files')
            );

            $user->avatar = $file_path;
        }

        $original_user_state = $user->state;

        // Convert transferred hours to minutes
        $req_data['transfer_time'] *= 60;

        $user = $this->userRepo->update($req_data, $id);

        if ($original_user_state !== $user->state) {
            $this->userSvc->changeUserState($user, $original_user_state);
        }

        // Delete all of the roles and then re-attach the valid ones
        if (!empty($request->input('roles'))) {
            DB::table('role_user')->where('user_id', $id)->delete();
            foreach ($request->input('roles') as $key => $value) {
                $user->attachRole($value);
            }
        }

        Flash::success('User updated successfully.');

        return redirect(route('admin.users.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $user = $this->userRepo->findWithoutFail($id);
        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }

        $this->userSvc->removeUser($user);
        Flash::success('User deleted successfully.');

        return redirect(route('admin.users.index'));
    }

    /**
     * Remove the award from a user
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed                    $id
     * @param mixed                    $award_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy_user_award($id, $award_id, Request $request)
    {
        $userAward = UserAward::where(['user_id' => $id, 'award_id' => $award_id]);
        if (empty($userAward)) {
            Flash::error('The user award could not be found');
            return redirect()->back();
        }

        $userAward->delete();

        return redirect()->back();
    }

    /**
     * Regenerate the user's API key
     *
     * @param         $id
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function regen_apikey($id, Request $request)
    {
        $user = User::find($id);
        Log::info('Regenerating API key "'.$user->ident.'"');

        $user->api_key = Utils::generateApiKey();
        $user->save();

        flash('New API key generated!')->success();

        return redirect(route('admin.users.edit', [$id]));
    }
}
