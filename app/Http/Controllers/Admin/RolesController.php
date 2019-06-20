<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateAirlineRequest;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateAirlineRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Interfaces\Controller;
use App\Repositories\RoleRepository;
use App\Repositories\PermissionsRepository;
use App\Support\Countries;
use Flash;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AirlinesController
 */
class RolesController extends Controller
{
    private $permsRepo;
    private $rolesRepo;

    /**
     * AirlinesController constructor.
     *
     * @param PermissionsRepository $permsRepo
     * @param RoleRepository        $rolesRepo
     */
    public function __construct(PermissionsRepository $permsRepo, RoleRepository $rolesRepo)
    {
        $this->permsRepo = $permsRepo;
        $this->rolesRepo = $rolesRepo;
    }

    /**
     * Display a listing of the Airlines.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index(Request $request)
    {
        $this->rolesRepo->pushCriteria(new RequestCriteria($request));
        $roles = $this->rolesRepo->findWhere(['read_only' => false]);

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new Airlines.
     */
    public function create()
    {
        return view('admin.roles.create', [
            'permissions' => $this->permsRepo->all(),
        ]);
    }

    /**
     * Store a newly created Airlines in storage.
     *
     * @param CreateRoleRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateRoleRequest $request)
    {
        $input = $request->all();

        // Create a slug using the display name provided
        $input['name'] = str_slug($input['display_name']);

        $this->rolesRepo->create($input);

        Flash::success('Role saved successfully.');
        return redirect(route('admin.roles.index'));
    }

    /**
     * Display the specified role
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $roles = $this->rolesRepo->findWithoutFail($id);

        if (empty($roles)) {
            Flash::error('Role not found');
            return redirect(route('admin.roles.index'));
        }

        return view('admin.roles.show', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for editing the specified roles
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $role = $this->rolesRepo->findWithoutFail($id);

        if (empty($role)) {
            Flash::error('Role not found');
            return redirect(route('admin.role.index'));
        }

        return view('admin.roles.edit', [
            'role'        => $role,
            'permissions' => $this->permsRepo->all(),
        ]);
    }

    /**
     * Update the specified Airlines in storage.
     *
     * @param int               $id
     * @param UpdateRoleRequest $request
     *
     * @return Response
     *@throws \Prettus\Validator\Exceptions\ValidatorException
     *
     */
    public function update($id, UpdateRoleRequest $request)
    {
        $role = $this->rolesRepo->findWithoutFail($id);

        if (empty($role)) {
            Flash::error('Role not found');
            return redirect(route('admin.roles.index'));
        }

        $this->rolesRepo->update($request->all(), $id);

        // Update the permissions, filter out null/invalid values
        $perms = collect($request->permissions)->filter(static function ($v, $k) {
            return $v;
        });

        $role->permissions()->sync($perms);

        Flash::success('Roles updated successfully.');
        return redirect(route('admin.roles.index'));
    }

    /**
     * Remove the specified Airlines from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $roles = $this->rolesRepo->findWithoutFail($id);

        if (empty($roles)) {
            Flash::error('Role not found');
            return redirect(route('admin.roles.index'));
        }

        $this->rolesRepo->delete($id);

        Flash::success('Role deleted successfully.');
        return redirect(route('admin.roles.index'));
    }
}
