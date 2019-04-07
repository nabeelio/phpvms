<?php

namespace Modules\Installer\Http\Controllers;

use App\Interfaces\Controller;
use App\Services\Installer\MigrationService;
use Illuminate\Http\Request;
use Log;


/**
 * Class UpdaterController
 * @package Modules\Installer\Http\Controllers
 */
class UpdaterController extends Controller
{
    private $migrationSvc;

    /**
     * UpdaterController constructor.
     * @param MigrationService $migrationSvc
     */
    public function __construct(
        MigrationService $migrationSvc
    ) {
        $this->migrationSvc = $migrationSvc;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('installer::update/index-start');
    }

    /**
     * Step 1. Check if there's an update available. Check if there
     * are any unrun migrations
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function step1(Request $request)
    {
        $migrations = $this->migrationSvc->migrationsAvailable();
        if(\count($migrations) > 0) {
            Log::info('No migrations found');
        }

        return view('installer::update/steps/step1-update-available');
    }

    /**
     * Step 2 Run all of the migrations
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function run_migrations(Request $request)
    {
        Log::info('Update: run_migrations', $request->post());

        $migrations = $this->migrationSvc->migrationsAvailable();
        if(\count($migrations) === 0) {
            $this->migrationSvc->syncAllSeeds();
            return view('installer::update/steps/step3-update-complete');
        }

        $output = $this->migrationSvc->runAllMigrations();
        $this->migrationSvc->syncAllSeeds();

        return view('installer::update/steps/step2-migrations-done', [
            'console_output' => $output,
        ]);
    }

    /**
     * Final step
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function complete(Request $request)
    {
        return redirect('/login');
    }
}
