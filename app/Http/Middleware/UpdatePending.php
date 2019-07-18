<?php
/**
 * Determine if an update is pending by checking if there are available migrations
 * Redirect to the updater if there are. Done as middlware so it can happen before
 * any authentication checks when someone goes to the admin panel
 */

namespace App\Http\Middleware;

use App\Services\Installer\MigrationService;
use Closure;

class UpdatePending
{
    private $migrationSvc;

    /**
     * @param MigrationService $migrationSvc
     */
    public function __construct(MigrationService $migrationSvc)
    {
        $this->migrationSvc = $migrationSvc;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (count($this->migrationSvc->migrationsAvailable()) > 0) {
            return redirect('/update/step1');
        }

        return $next($request);
    }
}
