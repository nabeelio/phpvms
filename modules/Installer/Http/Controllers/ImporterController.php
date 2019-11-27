<?php

namespace Modules\Installer\Http\Controllers;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\Installer\Exceptions\ImporterNextRecordSet;
use Modules\Installer\Exceptions\StageCompleted;
use Modules\Installer\Services\Importer\ImporterService;

class ImporterController extends Controller
{
    private $importerSvc;

    public function __construct(ImporterService $importerSvc)
    {
        $this->importerSvc = $importerSvc;
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
        // Save the credentials to use later
        $this->importerSvc->saveCredentialsFromRequest($request);

        // Send it to run, step1
        return redirect(route('importer.run').'?stage=stage1&start=0');
    }

    /**
     * Run the importer. Pass in query string with a few different parameters:
     *
     * stage=STAGE NAME
     * start=record_start
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function run(Request $request)
    {
        $stage = $request->get('stage');
        $start = $request->get('start');

        Log::info('Starting stage '.$stage.' from offset '.$start);

        try {
            $this->importerSvc->run($stage, $start);
        }

        // The importer wants to move onto the next set of records, so refresh this page and continue
        catch (ImporterNextRecordSet $e) {
            Log::info('Getting more records for stage '.$stage.', starting at '.$e->nextOffset);
            return redirect(route('importer.run').'?stage='.$stage.'&start='.$e->nextOffset);
        }

        // This stage is completed, so move onto the next one
        catch (StageCompleted $e) {
            if ($e->nextStage === 'complete') {
                return view('installer::importer/complete');
            }

            Log::info('Completed stage '.$stage.', redirect to '.$e->nextStage);
            return redirect(route('importer.run').'?stage='.$e->nextStage.'&start=0');
        }
    }
}
