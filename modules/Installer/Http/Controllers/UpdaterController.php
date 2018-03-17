<?php

namespace Modules\Installer\Http\Controllers;

use Log;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Modules\Installer\Services\MigrationService;

class UpdaterController extends Controller
{
    protected $migrationSvc;

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
     */
    public function step1(Request $request)
    {
        $migrations = $this->migrationSvc->migrationsAvailable();
        if(\count($migrations) > 0) {
            return view('installer::update/steps/step1-update-available');
        }

        Log::info('No migrations found');
        return view('installer::update/steps/step1-no-update');
    }

    /**
     * Step 2 Run all of the migrations
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function run_migrations(Request $request)
    {
        Log::info('Update: run_migrations', $request->post());

        $migrations = $this->migrationSvc->migrationsAvailable();
        if(\count($migrations) === 0) {
            return redirect(route('update.complete'));
        }

        $output = $this->migrationSvc->runAllMigrations();

        return view('installer::update/steps/step2-migrations-done', [
            'console_output' => $output,
        ]);
    }

    /**
     * Final step
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function complete(Request $request)
    {
        return redirect('/login');
    }
}
