<?php

namespace App\Http\Controllers\System;

use App\Contracts\Controller;
use App\Services\ImporterService;
use App\Services\Installer\DatabaseService;
use App\Support\Utils;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImporterController extends Controller
{
    private DatabaseService $dbSvc;
    private ImporterService $importerSvc;

    public function __construct(DatabaseService $dbSvc, ImporterService $importerSvc)
    {
        $this->dbSvc = $dbSvc;
        $this->importerSvc = $importerSvc;

        Utils::disableDebugToolbar();
    }

    /**
     * Show the main page for the importer; show form for the admin email
     * and the credentials for the other database
     *
     * @return mixed
     */
    public function index()
    {
        return view('system.importer.step1-configure');
    }

    protected function testDb(Request $request)
    {
        $this->dbSvc->checkDbConnection(
            $request->post('db_conn'),
            $request->post('db_host'),
            $request->post('db_port'),
            $request->post('db_name'),
            $request->post('db_user'),
            $request->post('db_pass')
        );
    }

    /**
     * Check the database connection
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function dbtest(Request $request)
    {
        $status = 'success';  // success|warn|danger
        $message = 'Database connection looks good!';

        try {
            $this->testDb($request);
        } catch (Exception $e) {
            $status = 'danger';
            $message = 'Failed! '.$e->getMessage();
        }

        return view('system.importer.dbtest', [
            'status'  => $status,
            'message' => $message,
        ]);
    }

    /**
     * The post from the above
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function config(Request $request)
    {
        try {
            $this->testDb($request);

            // Save the credentials to use later
            $this->importerSvc->saveCredentialsFromRequest($request);

            // Generate the import manifest
            $manifest = $this->importerSvc->generateImportManifest();
        } catch (Exception $e) {
            Log::error($e->getMessage());

            // Send it to run, step1
            return view('system.importer.error', [
                'error' => $e->getMessage(),
            ]);
        }

        // Send it to run, step1
        return view('system.importer.step2-processing', [
            'manifest' => $manifest,
        ]);
    }

    /**
     * Run the importer. Pass in query string with a few different parameters:
     *
     * stage=STAGE NAME
     * start=record_start
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function run(Request $request)
    {
        $importer = $request->input('importer');
        $start = $request->input('start');

        Log::info('Starting stage '.$importer.' from offset '.$start);

        $this->importerSvc->run($importer, $start);

        return response()->json([
            'message' => 'completed',
        ]);
    }

    /**
     * Complete the import
     */
    public function complete()
    {
        return redirect('/');
    }
}
