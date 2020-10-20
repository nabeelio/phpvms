<?php

namespace App\Http\Controllers\System;

use App\Contracts\Controller;
use App\Repositories\KvpRepository;
use App\Services\AnalyticsService;
use App\Services\Installer\InstallerService;
use App\Services\Installer\MigrationService;
use App\Services\Installer\SeederService;
use Codedge\Updater\UpdaterManager;
use function count;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UpdateController extends Controller
{
    private $analyticsSvc;
    private $installerSvc;
    private $kvpRepo;
    private $migrationSvc;
    private $seederSvc;
    private $updateManager;

    /**
     * @param AnalyticsService $analyticsSvc
     * @param InstallerService $installerSvc
     * @param KvpRepository    $kvpRepo
     * @param MigrationService $migrationSvc
     * @param SeederService    $seederSvc
     * @param UpdaterManager   $updateManager
     */
    public function __construct(
        AnalyticsService $analyticsSvc,
        InstallerService $installerSvc,
        KvpRepository $kvpRepo,
        MigrationService $migrationSvc,
        SeederService $seederSvc,
        UpdaterManager $updateManager
    ) {
        $this->analyticsSvc = $analyticsSvc;
        $this->migrationSvc = $migrationSvc;
        $this->seederSvc = $seederSvc;
        $this->installerSvc = $installerSvc;
        $this->kvpRepo = $kvpRepo;
        $this->updateManager = $updateManager;
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

    /**
     * Show the update page with the latest version
     *
     * @return Factory|View
     */
    public function updater()
    {
        $version = $this->kvpRepo->get('latest_version_tag');

        return view('system.updater.downloader/downloader', [
            'version' => $version,
        ]);
    }

    /**
     * Download the actual update and then forward the user to the updater page
     *
     * @return mixed
     */
    public function update_download()
    {
        $version = $this->kvpRepo->get('latest_version_tag');
        if (empty($version)) {
            return view('system.updater.steps.step1-no-update');
        }

        $release = $this->updateManager->source('github')->fetch($version);
        $this->updateManager->source('github')->update($release);
        $this->analyticsSvc->sendUpdate();

        Log::info('Update completed to '.$version.', redirecting');
        return redirect('/update');
    }
}
