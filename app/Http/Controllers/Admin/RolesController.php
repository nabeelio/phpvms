<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\PermissionsRepository;
use App\Repositories\RoleRepository;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class RolesController extends Controller
{
    private PermissionsRepository $permsRepo;
    private RoleRepository $rolesRepo;
    private RoleService $roleSvc;

    /**
     * AirlinesController constructor.
     *
     * @param PermissionsRepository $permsRepo
     * @param RoleRepository        $rolesRepo
     * @param                       $roleSvc
     */
    public function __construct(
        PermissionsRepository $permsRepo,
        RoleRepository $rolesRepo,
        RoleService $roleSvc
    ) {
        $this->permsRepo = $permsRepo;
        $this->rolesRepo = $rolesRepo;
        $this->roleSvc = $roleSvc;
    }

    /**
     * Display a listing of the Airlines.
     *
     * @param Request $request
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->rolesRepo->pushCriteria(new RequestCriteria($request));
        $roles = $this->rolesRepo->withCount('users')->findWhere(['read_only' => false]);

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
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
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
        $role = $this->rolesRepo->withCount('users')->with('users')->findWithoutFail($id);

        if (empty($role)) {
            Flash::error('Role not found');
            return redirect(route('admin.role.index'));
        }

        return view('admin.roles.edit', [
            'role'        => $role,
            'users'       => $role->users,
            'users_count' => $role->users_count,
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
     */
    public function update($id, UpdateRoleRequest $request)
    {
        $role = $this->rolesRepo->findWithoutFail($id);

        if (empty($role)) {
            Flash::error('Role not found');
            return redirect(route('admin.roles.index'));
        }

        $this->roleSvc->updateRole($role, $request->all());
        $this->roleSvc->setPermissionsForRole($role, $request->permissions);

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
