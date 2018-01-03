<?php

namespace App\Http\Controllers\Admin;

use DB;
use Hash;
use Log;
use Flash;
use Response;
use Jackiedo\Timezonelist\Facades\Timezonelist;
use Illuminate\Http\Request;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use App\Services\UserService;

use App\Models\Airport;
use App\Models\Airline;
use App\Models\Rank;
use App\Models\Role;

class UserController extends BaseController
{
    /** @var  UserRepository */
    private $userRepo,
            $userSvc;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepo
     */
    public function __construct(
        UserRepository $userRepo,
        UserService $userSvc
    ) {
        $this->userSvc = $userSvc;
        $this->userRepo = $userRepo;
    }

    public function index(Request $request)
    {
        $users = $this->userRepo->searchCriteria($request, false)
                      ->orderBy('created_at', 'desc')
                      ->paginate();

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new User.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.user.create', [
            'airlines' => Airline::all()->pluck('name', 'id'),
        ]);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->all();
        $User = $this->userRepo->create($input);

        Flash::success('User saved successfully.');
        return redirect(route('admin.users.index'));
    }

    /**
     * Display the specified User.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = $this->userRepo->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }

        return view('admin.users.show', [
            'user' => $user,
            'airlines' => Airline::all(),
            'timezones' => Timezonelist::toArray(),
            'airports' => Airport::all()->pluck('icao', 'id'),
            'ranks' => Rank::all()->pluck('name', 'id'),
            'roles' => Role::all()->pluck('name', 'id'),
        ]);
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $user = $this->userRepo->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }

        return view('admin.users.edit', [
            'user' => $user,
            'timezones' => Timezonelist::toArray(),
            'airports' => Airport::all()->pluck('icao', 'id'),
            'airlines' => Airline::all()->pluck('name', 'id'),
            'ranks' => Rank::all()->pluck('name', 'id'),
            'roles' => Role::all()->pluck('name', 'id'),
        ]);
    }

    /**
     * Update the specified User in storage.
     *
     * @param int               $id
     * @param UpdateUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = $this->userRepo->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }

        $req_data = $request->all();
        if(!$request->filled('password')) {
            unset($req_data['password']);
        } else {
            $req_data['password'] = Hash::make($req_data['password']);
        }

        $original_user_state = $user->state;

        $user = $this->userRepo->update($req_data, $id);

        if($original_user_state !== $user->state) {
            $this->userSvc->changeUserState($user, $original_user_state);
        }

        # Delete all of the roles and then re-attach the valid ones
        DB::table('role_user')->where('user_id',$id)->delete();
        foreach ($request->input('roles') as $key => $value) {
            $user->attachRole($value);
        }

        Flash::success('User updated successfully.');
        return redirect(route('admin.users.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $user = $this->userRepo->findWithoutFail($id);

        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }

        $this->userRepo->delete($id);

        Flash::success('User deleted successfully.');
        return redirect(route('admin.users.index'));
    }
}
