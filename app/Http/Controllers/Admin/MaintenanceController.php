<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Services\CronService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Laracasts\Flash\Flash;

class MaintenanceController extends Controller
{
    private $cronSvc;

    public function __construct(CronService $cronSvc)
    {
        $this->cronSvc = $cronSvc;
    }

    public function index()
    {
        return view('admin.maintenance.index', [
            'cron_path'           => $this->cronSvc->getCronExecString(),
            'cron_problem_exists' => $this->cronSvc->cronProblemExists(),
        ]);
    }

    /**
     * Clear caches depending on the type passed in
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
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
}
