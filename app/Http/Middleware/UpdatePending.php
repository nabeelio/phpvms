<?php

namespace App\Http\Middleware;

use App\Services\Installer\InstallerService;
use Closure;

/**
 * Determine if an update is pending by checking in with the Installer service
 */
class UpdatePending
{
    private $installerSvc;

    public function __construct(InstallerService $installerSvc)
    {
        $this->installerSvc = $installerSvc;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->installerSvc->isUpgradePending()) {
            return redirect('/update/step1');
        }

        return $next($request);
    }
}
