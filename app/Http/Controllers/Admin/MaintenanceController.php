<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Repositories\KvpRepository;
use App\Services\CronService;
use App\Services\VersionService;
use App\Support\Utils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;

class MaintenanceController extends Controller
{
    private CronService $cronSvc;
    private KvpRepository $kvpRepo;
    private VersionService $versionSvc;

    public function __construct(
        CronService $cronSvc,
        KvpRepository $kvpRepo,
        VersionService $versionSvc
    ) {
        $this->cronSvc = $cronSvc;
        $this->kvpRepo = $kvpRepo;
        $this->versionSvc = $versionSvc;
    }

    public function index()
    {
        // Get the cron URL
        $cron_id = setting('cron.random_id');
        $cron_url = empty($cron_id) ? 'Not enabled' : url(route('api.maintenance.cron', $cron_id));

        return view('admin.maintenance.index', [
            'cron_url'            => $cron_url,
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
     * Enable the cron, or if it's enabled, change the ID that is used
     *
     * @param Request $request
     */
    public function cron_enable(Request $request)
    {
        $id = Utils::generateNewId(24);
        setting_save('cron.random_id', $id);

        Flash::success('Web cron refreshed!');
        return redirect(route('admin.maintenance.index'));
    }

    /**
     * Disable the web cron
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function cron_disable(Request $request)
    {
        setting_save('cron.random_id', '');

        Flash::success('Web cron disabled!');
        return redirect(route('admin.maintenance.index'));
    }
}
