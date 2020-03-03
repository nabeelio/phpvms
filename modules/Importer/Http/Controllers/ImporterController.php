<?php

namespace Modules\Importer\Http\Controllers;

use App\Contracts\Controller;
use App\Services\Installer\DatabaseService;
use App\Services\Installer\InstallerService;
use App\Support\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Importer\Services\ImporterService;

class ImporterController extends Controller
{
    private $dbSvc;
    private $importerSvc;

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
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        foreach (getallheaders() as $name => $value) {
            Log::info("$name: $value\n");
        }

        foreach ($_SERVER as $name => $value) {
            Log::info("$name: $value\n");
        }

        return view('importer::step1-configure');
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
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function dbtest(Request $request)
    {
        $status = 'success';  // success|warn|danger
        $message = 'Database connection looks good!';

        try {
            $this->testDb($request);
        } catch (\Exception $e) {
            $status = 'danger';
            $message = 'Failed! '.$e->getMessage();
        }

        return view('importer::dbtest', [
            'status'  => $status,
            'message' => $message,
        ]);
    }

    /**
     * The post from the above
     *
     * @param \Illuminate\Http\Request $request
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
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            // Send it to run, step1
            return view('importer::error', [
                'error' => $e->getMessage(),
            ]);
        }

        // Send it to run, step1
        return view('importer::step2-processing', [
            'manifest' => $manifest,
        ]);
    }

    /**
     * Run the importer. Pass in query string with a few different parameters:
     *
     * stage=STAGE NAME
     * start=record_start
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
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
        $installerSvc = app(InstallerService::class);
        $installerSvc->disableInstallerModules();

        return redirect('/');
    }
}
