<?php

namespace App\Services;

use App\Models\Enums\AnalyticsDimensions;
use DB;
use Irazasyed\LaravelGAMP\Facades\GAMP;
use Log;
use PDO;

class AnalyticsService
{
    /**
     * Send out some stats about the install
     */
    public function sendInstall()
    {
        if(config('app.analytics') === false) {
            return;
        }

        # some analytics
        $gamp = GAMP::setClientId(uniqid('', true));
        $gamp->setDocumentPath('/install');

        $gamp->setCustomDimension(PHP_VERSION, AnalyticsDimensions::PHP_VERSION);

        # figure out database version
        $pdo = DB::connection()->getPdo();
        $gamp->setCustomDimension(
            strtolower($pdo->getAttribute(PDO::ATTR_SERVER_VERSION)),
            AnalyticsDimensions::DATABASE_VERSION
        );

        try {
            $gamp->sendPageview();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

}
