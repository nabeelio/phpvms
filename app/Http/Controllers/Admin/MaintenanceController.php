<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Repositories\KvpRepository;
use App\Services\CronService;
use App\Services\VersionService;
use Codedge\Updater\UpdaterManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class MaintenanceController extends Controller
{
    private $cronSvc;
    private $kvpRepo;
    private $updateManager;
    private $versionSvc;

    public function __construct(
        CronService $cronSvc,
        KvpRepository $kvpRepo,
        UpdaterManager $updateManager,
        VersionService $versionSvc
    ) {
        $this->cronSvc = $cronSvc;
        $this->kvpRepo = $kvpRepo;
        $this->updateManager = $updateManager;
        $this->versionSvc = $versionSvc;
    }

    public function index()
    {
        return view('admin.maintenance.index', [
            'cron_path'           => $this->cronSvc->getCronExecString(),
            'cron_problem_exists' => $this->cronSvc->cronProblemExists(),
            'new_version'         => $this->kvpRepo->get('new_version_available', false),
            'new_version_tag'     => $this->kvpRepo->get('latest_version_tag'),
        ]);
    }

    /**
     * Clear caches depending on the type passed in
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function cache(Request $request)
    {
        $calls = [];
        $type = $request->get('type');

        // When clearing the application, clear the config and the app itself
        if ($type === 'application' || $type === 'all') {
            $calls[] = 'config:cache';
            $calls[] = 'cache:clear';
            $calls[] = 'route:cache';
            $calls[] = 'clear-compiled';
        }

        // If we want to clear only the views but keep everything else
        if ($type === 'views' || $type === 'all') {
            $calls[] = 'view:clear';
        }

        foreach ($calls as $call) {
            Artisan::call($call);
        }

        Flash::success('Cache cleared!');
        return redirect(route('admin.maintenance.index'));
    }

    /**
     * Force an update check
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function forcecheck(Request $request)
    {
        $this->versionSvc->isNewVersionAvailable();

        $new_version_avail = $this->kvpRepo->get('new_version_available', false);
        $new_version_tag = $this->kvpRepo->get('latest_version_tag');

        Log::info('Force check, available='.$new_version_avail.', tag='.$new_version_tag);

        if (!$new_version_avail) {
            Flash::success('No new version available');
        } else {
            Flash::success('New version available: '.$new_version_tag);
        }

        return redirect(route('admin.maintenance.index'));
    }

    /**
     * Update the phpVMS install
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function update(Request $request)
    {
        $new_version_tag = $this->kvpRepo->get('latest_version_tag');
        Log::info('Attempting to update to '.$new_version_tag);
        return redirect('/update/downloader');
    }
}
