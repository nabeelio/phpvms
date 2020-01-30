<?php

namespace App\Services\Installer;

use App\Contracts\Service;
use Illuminate\Support\Facades\Artisan;
use Nwidart\Modules\Facades\Module;

class InstallerService extends Service
{
    private $migrationSvc;
    private $seederSvc;

    /**
     * @param $migrationSvc
     * @param $seederSvc
     */
    public function __construct(MigrationService $migrationSvc, SeederService $seederSvc)
    {
        $this->migrationSvc = $migrationSvc;
        $this->seederSvc = $seederSvc;
    }

    /**
     * Check to see if there is an upgrade pending by checking the migrations or seeds
     *
     * @return bool
     */
    public function isUpgradePending(): bool
    {
        if (count($this->migrationSvc->migrationsAvailable()) > 0) {
            return true;
        }

        if ($this->seederSvc->seedsPending()) {
            return true;
        }

        return false;
    }

    /**
     * Clear whatever caches we can by calling Artisan
     */
    public function clearCaches(): void
    {
        $commands = [
            'clear-compiled',
            'cache:clear',
            'route:clear',
            'view:clear',
        ];

        foreach ($commands as $cmd) {
            Artisan::call($cmd);
        }
    }

    /**
     * Disable the installer and importer modules
     */
    public function disableInstallerModules()
    {
        $module = Module::find('installer');
        $module->disable();

        $module = Module::find('importer');
        $module->disable();
    }
}
