<?php

namespace App\Http\Controllers\System;

use App\Contracts\Controller;
use App\Services\Installer\InstallerService;
use App\Services\Installer\MigrationService;
use App\Services\Installer\SeederService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

use function count;

class UpdateController extends Controller
{
    /**
     * @param InstallerService $installerSvc
     * @param MigrationService $migrationSvc
     * @param SeederService    $seederSvc
     */
    public function __construct(
        private readonly InstallerService $installerSvc,
        private readonly MigrationService $migrationSvc,
        private readonly SeederService $seederSvc
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        return view('system.updater.index-start');
    }

    /**
     * Step 1. Check if there's an update available. Check if there
     * are any unrun migrations
     *
     * @return View
     */
    public function step1(): View
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
     * @return View
     */
    public function run_migrations(Request $request): View
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
     * @return RedirectResponse
     */
    public function complete(): RedirectResponse
    {
        return redirect('/admin');
    }
}
