<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Repositories\UserRepository;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Flash;
use Jackiedo\Timezonelist\Facades\Timezonelist;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

use App\Models\Airport;
use App\Models\Airline;
use App\Models\Rank;
use App\Models\Role;


class UserController extends BaseController
{
    /** @var  UserRepository */
    private $userRepo;

    /**
     * UserController constructor.
     *
     * @param UserRepository $userRepo
     */
    public function __construct(
        UserRepository $userRepo
    ) {
        $this->userRepo = $userRepo;
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->userRepo->pushCriteria(new RequestCriteria($request));
        $Users = $this->userRepo->all();

        return view('admin.users.index', [
            'users' => $Users,
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
        $users = $this->userRepo->findWithoutFail($id);

        if (empty($users)) {
            Flash::error('User not found');
            return redirect(route('admin.users.index'));
        }

        return view('admin.users.show', [
            'users' => $users,
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

        $user = $this->userRepo->update($req_data, $id);

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
