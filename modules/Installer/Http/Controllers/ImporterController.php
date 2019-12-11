<?php

namespace Modules\Installer\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Installer\Services\Importer\ImporterService;

class ImporterController extends Controller
{
    private $importerSvc;

    public function __construct(ImporterService $importerSvc)
    {
        $this->importerSvc = $importerSvc;

        app('debugbar')->disable();
    }

    /**
     * Show the main page for the importer; show form for the admin email
     * and the credentials for the other database
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        app('debugbar')->disable(); // saves the query logging

        return view('installer::importer/step1-configure');
    }

    /**
     * The post from the above
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function config(Request $request)
    {
        app('debugbar')->disable(); // saves the query logging

        try {
            // Save the credentials to use later
            $this->importerSvc->saveCredentialsFromRequest($request);

            // Generate the import manifest
            $manifest = $this->importerSvc->generateImportManifest();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            // Send it to run, step1
            return view('installer::importer/error', [
                'error' => $e->getMessage(),
            ]);
        }

        // Send it to run, step1
        return view('installer::importer/step2-processing', [
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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function run(Request $request)
    {
        app('debugbar')->disable(); // saves the query logging

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
