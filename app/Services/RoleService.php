<?php

namespace App\Services;

use App\Contracts\Service;
use App\Models\Role;
use App\Repositories\RoleRepository;

class RoleService extends Service
{
    private RoleRepository $roleRepo;

    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    /**
     * Update a role with the given attributes
     *
     * @param Role  $role
     * @param array $attrs
     *
     * @return Role
     */
    public function updateRole(Role $role, array $attrs)
    {
        $role->update($attrs);
        $role->save();

        return $role;
    }

    /**
     * @param Role  $role
     * @param array $permissions
     */
    public function setPermissionsForRole(Role $role, array $permissions)
    {
        // Update the permissions, filter out null/invalid values
        $perms = collect($permissions)->filter(static function ($v, $k) {
            return !empty($v);
        });

        $role->permissions()->sync($perms);
    }
}
