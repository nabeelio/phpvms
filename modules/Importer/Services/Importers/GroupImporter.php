<?php

namespace Modules\Importer\Services\Importers;

use App\Models\Role;
use App\Services\RoleService;
use Modules\Importer\Services\BaseImporter;

/**
 * Imports the groups into the permissions feature(s)
 */
class GroupImporter extends BaseImporter
{
    protected $table = 'groups';
    protected $idField = 'groupid';

    /**
     * Permissions in the legacy system, mapping them to the current system
     */
    protected $legacy_permission_set = [
        'EDIT_NEWS'              => 0x1,
        'EDIT_PAGES'             => 0x2,
        'EDIT_DOWNLOADS'         => 0x4,
        'EMAIL_PILOTS'           => 0x8,
        'EDIT_AIRLINES'          => 0x10, //
        'EDIT_FLEET'             => 0x20, //
        'EDIT_SCHEDULES'         => 0x80, //
        'IMPORT_SCHEDULES'       => 0x100, //
        'MODERATE_REGISTRATIONS' => 0x200,
        'EDIT_PILOTS'            => 0x400, //
        'EDIT_GROUPS'            => 0x800,
        'EDIT_RANKS'             => 0x1000, //
        'EDIT_AWARDS'            => 0x2000, //
        'MODERATE_PIREPS'        => 0x4000, //
        'VIEW_FINANCES'          => 0x8000, //
        'EDIT_EXPENSES'          => 0x10000, //
        'EDIT_SETTINGS'          => 0x20000, //
        'EDIT_PIREPS_FIELDS'     => 0x40000, //
        'EDIT_PROFILE_FIELDS'    => 0x80000, //
        'EDIT_VACENTRAL'         => 0x100000,
        'ACCESS_ADMIN'           => 0x2000000,
        'FULL_ADMIN'             => 35651519, //
    ];

    /**
     * Map a legacy value over to one of the current permission values
     */
    protected $legacy_to_permission = [
        'FULL_ADMIN'             => 'admin',
        'EDIT_AIRLINES'          => 'airlines',
        'EDIT_AWARDS'            => 'awards',
        'EDIT_FLEET'             => 'fleet',
        'EDIT_EXPENCES'          => 'finances',
        'VIEW_FINANCES'          => 'finances',
        'EDIT_SCHEDULES'         => 'flights',
        'EDIT_PILOTS'            => 'users',
        'EDIT_PROFILE_FIELDS'    => 'users',
        'EDIT_SETTINGS'          => 'settings',
        'MODERATE_PIREPS'        => 'pireps',
        'EDIT_PIREPS_FIELDS'     => 'pireps',
        'EDIT_RANKS'             => 'ranks',
        'MODERATE_REGISTRATIONS' => 'users',
    ];

    public function run($start = 0)
    {
        $this->comment('--- ROLES/GROUPS IMPORT ---');

        $roleSvc = app(RoleService::class);

        $count = 0;
        $rows = $this->db->readRows($this->table, $this->idField, $start);
        foreach ($rows as $row) {
            // Legacy "administrator" role is now "admin", just map that 1:1
            if (strtolower($row->name) === 'administrators') {
                $role = Role::where('name', 'admin')->first();
                $this->idMapper->addMapping('group', $row->groupid, $role->id);
                continue;
            }

            $name = str_slug($row->name);
            $role = Role::firstOrCreate(
                ['name' => $name],
                ['display_name' => $row->name]
            );

            $this->idMapper->addMapping('group', $row->groupid, $role->id);

            // See if the permission set mask contains one of the mappings above
            // Add all of the ones which apply, and then set them on the new role
            $permissions = [];
            foreach ($this->legacy_permission_set as $legacy_name => $mask) {
                if (($row->permissions & $mask) === true) {
                    if (!array_key_exists($legacy_name, $this->legacy_to_permission)) {
                        continue;
                    }

                    $permissions[] = $this->legacy_to_permission[$legacy_name];
                }
            }

            $roleSvc->setPermissionsForRole($role, $permissions);

            if ($role->wasRecentlyCreated) {
                $count++;
            }
        }

        $this->info('Imported '.$count.' ranks');
    }
}
