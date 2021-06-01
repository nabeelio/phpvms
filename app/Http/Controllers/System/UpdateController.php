<?php

namespace App\Http\Controllers\System;

use App\Contracts\Controller;
use App\Services\Installer\InstallerService;
use App\Services\Installer\MigrationService;
use App\Services\Installer\SeederService;
use function count;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    private $installerSvc;
    private $migrationSvc;
    private $seederSvc;

    /**
     * @param InstallerService $installerSvc
     * @param MigrationService $migrationSvc
     * @param SeederService    $seederSvc
     */
    public function __construct(
        InstallerService $installerSvc,
        MigrationService $migrationSvc,
        SeederService $seederSvc
    ) {
        $this->migrationSvc = $migrationSvc;
        $this->seederSvc = $seederSvc;
        $this->installerSvc = $installerSvc;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('system.updater.index-start');
    }

    /**
     * Step 1. Check if there's an update available. Check if there
     * are any unrun migrations
     *
     * @return mixed
     */
    public function step1()
    {
        $this->installerSvc->clearCaches();

        if ($this->installerSvc->isUpgradePending()) {
            Log::info('Upgrade is pending');
        }

        return view('system.updater.steps.step1-update-available');
    }

    /**
     * Step 2 Run all of the migrations
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function run_migrations(Request $request)
    {
        Log::info('Update: run_migrations', $request->post());

        $migrations = $this->migrationSvc->migrationsAvailable();
        if (count($migrations) === 0) {
            $this->seederSvc->syncAllSeeds();
            return view('system.updater.steps.step3-update-complete');
        }

        $output = $this->migrationSvc->runAllMigrations();
        $this->seederSvc->syncAllSeeds();

        return view('system.updater.steps.step2-migrations-done', [
            'console_output' => $output,
        ]);
    }

    /**
     * Final step
     *
     * @return mixed
     */
    public function complete()
    {
        return redirect('/admin');
    }
}
