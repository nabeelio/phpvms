<?php

namespace App\Services;

use App\Contracts\Service;
use App\Models\Enums\AnalyticsDimensions;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Irazasyed\LaravelGAMP\Facades\GAMP;
use PDO;

class AnalyticsService extends Service
{
    /**
     * Create a GAMP instance with a random ID
     *
     * @return mixed
     */
    private function getGAMPInstance()
    {
        return GAMP::setClientId(uniqid('', true));
    }

    /**
     * Send out some stats about the install, like the PHP and DB versions
     */
    public function sendInstall()
    {
        if (setting('general.telemetry') === false) {
            return;
        }

        // Generate a random client ID
        $gamp = $this->getGAMPInstance();

        $gamp->setDocumentPath('/install');

        // Send the PHP version
        $gamp->setCustomDimension(PHP_VERSION, AnalyticsDimensions::PHPVMS_VERSION);

        // Figure out the database version
        $pdo = DB::connection()->getPdo();
        $gamp->setCustomDimension(
            strtolower($pdo->getAttribute(PDO::ATTR_SERVER_VERSION)),
            AnalyticsDimensions::DATABASE_VERSION
        );

        // Send the PHPVMS Version
        $versionSvc = app(VersionService::class);
        $gamp->setCustomDimension(
            $versionSvc->getCurrentVersion(false),
            AnalyticsDimensions::PHP_VERSION
        );

        // Send that an install was done
        try {
            $gamp->sendPageview();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
