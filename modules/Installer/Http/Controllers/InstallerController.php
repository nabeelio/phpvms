<?php

namespace Modules\Installer\Http\Controllers;

use Illuminate\Database\QueryException;
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
     * Check if any of the items has been marked as failed
     * @param array $arr
     * @return bool
     */
    protected function allPassed(array $arr): bool
    {
        foreach($arr as $item) {
            if($item['passed'] === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Step 1. Check the modules and permissions
     */
    public function step1(Request $request)
    {
        $php_version = $this->reqService->checkPHPVersion();
        $extensions = $this->reqService->checkExtensions();
        $directories = $this->reqService->checkPermissions();

        # Only pass if all the items in the ext and dirs are passed
        $statuses = [
            $php_version['passed'] === true,
            $this->allPassed($extensions) === true,
            $this->allPassed($directories) === true
        ];

        # Make sure there are no false values
        $passed = ! in_array(false, $statuses, true);

        return view('installer::steps/step1-requirements', [
            'php' => $php_version,
            'extensions' => $extensions,
            'directories' => $directories,
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
     * Step 2a. Create the .env
     */
    public function envsetup(Request $request)
    {
        Log::info('ENV setup', $request->toArray());

        $this->envService->createEnvFile(
            $request->input('db_conn'),
            $request->input('db_host'),
            $request->input('db_port'),
            $request->input('db_name'),
            $request->input('db_user'),
            $request->input('db_pass')
        );

        # Needs to redirect so it can load the new .env
        Log::info('Redirecting to database setup');
        return redirect(route('installer.dbsetup'));
    }

    /**
     * Step 2b. Setup the database
     */
    public function dbsetup(Request $request)
    {
        try {
            $console_out = $this->dbService->setupDB($request->input('db_conn'));
        } catch(QueryException $e) {
            flash()->error($e->getMessage());
            return redirect(route('installer.step2'));
        }

        return view('installer::steps/step2a-completed', [
            'console_output' => $console_out
        ]);
    }

    /**
     * Step 3. Setup the admin user and initial settings
     */
    public function step3(Request $request)
    {

    }

    public function complete(Request $request)
    {
        return redirect('/');
    }
}
