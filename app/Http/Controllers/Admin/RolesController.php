<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Contracts\Controller;
use App\Repositories\PermissionsRepository;
use App\Repositories\RoleRepository;
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
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @throws \Prettus\Validator\Exceptions\ValidatorException
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
