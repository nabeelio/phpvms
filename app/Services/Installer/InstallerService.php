<?php

namespace App\Services\Installer;

use App\Contracts\Service;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class InstallerService extends Service
{
    /**
     * @param MigrationService $migrationSvc
     * @param SeederService    $seederSvc
     */
    public function __construct(
        private readonly MigrationService $migrationSvc,
        private readonly SeederService $seederSvc
    ) {
    }

    /**
     * Check to see if there is an upgrade pending by checking the migrations or seeds
     *
     * @return bool
     */
    public function isUpgradePending(): bool
    {
        $pendingMigrations = count($this->migrationSvc->migrationsAvailable());
        if ($pendingMigrations > 0) {
            Log::info('Found '.$pendingMigrations.' pending migrations, update available');
            return true;
        }

        if ($this->seederSvc->seedsPending()) {
            Log::info('Found seeds pending, update available');
            return true;
        }

        return false;
    }

    /**
     * Clear whatever caches we can by calling Artisan
     */
    public function clearCaches(): void
    {
        Artisan::call('optimize:clear');
    }
}
