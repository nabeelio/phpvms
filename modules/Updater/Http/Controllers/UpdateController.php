<?php

namespace Modules\Updater\Http\Controllers;

use App\Contracts\Controller;
use App\Repositories\KvpRepository;
use App\Services\Installer\InstallerService;
use App\Services\Installer\MigrationService;
use App\Services\Installer\SeederService;
use Codedge\Updater\UpdaterManager;
use function count;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    private $installerSvc;
    private $kvpRepo;
    private $migrationSvc;
    private $seederSvc;
    private $updateManager;

    /**
     * @param InstallerService $installerSvc
     * @param MigrationService $migrationSvc
     * @param SeederService    $seederSvc
     * @param KvpRepository    $kvpRepo
     * @param UpdaterManager   $updateManager
     */
    public function __construct(
        InstallerService $installerSvc,
        KvpRepository $kvpRepo,
        MigrationService $migrationSvc,
        SeederService $seederSvc,
        UpdaterManager $updateManager
    ) {
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
        return view('updater::index-start');
    }

    /**
     * Step 1. Check if there's an update available. Check if there
     * are any unrun migrations
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function step1(Request $request)
    {
        $this->installerSvc->clearCaches();

        if ($this->installerSvc->isUpgradePending()) {
            Log::info('Upgrade is pending');
        }

        return view('updater::steps/step1-update-available');
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
            return view('updater::steps/step3-update-complete');
        }

        $output = $this->migrationSvc->runAllMigrations();
        $this->seederSvc->syncAllSeeds();

        return view('updater::steps/step2-migrations-done', [
            'console_output' => $output,
        ]);
    }

    /**
     * Final step
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function complete(Request $request)
    {
        return redirect('/login');
    }

    /**
     * Show the update page with the latest version
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updater(Request $request)
    {
        $version = $this->kvpRepo->get('latest_version_tag');

        return view('updater::downloader/downloader', [
            'version' => $version,
        ]);
    }

    /**
     * Download the actual update and then forward the user to the updater page
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function update_download(Request $request)
    {
        $version = $this->kvpRepo->get('latest_version_tag');
        $this->updateManager->source('github')->update($version);

        Log::info('Update completed to '.$version.', redirecting');
        return redirect('/update');
    }
}
