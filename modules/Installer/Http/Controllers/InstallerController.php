<?php

namespace Modules\Installer\Http\Controllers;

use Log;
use Illuminate\Http\Request;

use App\Http\Controllers\AppBaseController;

use Modules\Installer\Services\DatabaseService;
use Modules\Installer\Services\EnvironmentService;
use Modules\Installer\Services\RequirementsService;


class InstallerController extends AppBaseController
{
    protected $dbService, $envService, $reqService;

    public function __construct(
        DatabaseService $dbService,
        EnvironmentService $envService,
        RequirementsService $reqService
    ) {
        $this->dbService = $dbService;
        $this->envService = $envService;
        $this->reqService = $reqService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('installer::index-start');
    }

    /**
     * Check the database connection
     */
    public function dbtest(Request $request)
    {
        $status = 'success';  # success|warn|danger
        $message = 'Database connection looks good!';

        try {
            $this->dbService->checkDbConnection(
                $request->input('db_conn'),
                $request->input('db_host'),
                $request->input('db_port'),
                $request->input('db_name'),
                $request->input('db_user'),
                $request->input('db_pass')
            );
        } catch (\Exception $e) {
            $status = 'danger';
            $message = 'Failed! ' . $e->getMessage();
        }

        return view('installer::flash/message', [
            'status' => $status,
            'message' => $message,
        ]);
    }

    /**
     * Step 1. Check the modules and permissions
     */
    public function step1(Request $request)
    {
        $passed = true;
        $php_version = $this->reqService->checkPHPVersion();
        if($php_version['passed'] === false) {
            $passed = false;
        }

        $extensions = $this->reqService->checkExtensions();
        foreach ($extensions as $ext) {
            if($ext['passed'] === false) {
                $passed = false;
            }
        }

        return view('installer::steps/step1-requirements', [
            'php' => $php_version,
            'extensions' => $extensions,
            'passed' => $passed,
        ]);
    }

    /**
     * Step 2. Database Setup
     */
    public function step2(Request $request)
    {
        $db_types = ['mysql' => 'mysql', 'sqlite' => 'sqlite'];
        return view('installer::steps/step2-db', [
            'db_types' => $db_types,
        ]);
    }

    /**
     * Step 2a. Do the config and setup
     */
    public function dbsetup(Request $request)
    {
        $log = [];

        Log::info('DB Setup', $request->toArray());

        $log[] = 'Creating environment file';
        $this->envService->createEnvFile(
            $request->input('db_conn'),
            $request->input('db_host'),
            $request->input('db_port'),
            $request->input('db_name'),
            $request->input('db_user'),
            $request->input('db_pass')
        );

        $log[] = 'Creating database';
        $this->dbService->setupDB();

        return redirect('/');
    }

    /**
     * Step 3. Setup the admin user and initial settings
     */
    public function step3(Request $request)
    {

    }
}
