<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class AnalyticsMetrics
 * Metrics IDs used in Google Analytics
 */
class AnalyticsMetrics extends Enum
{
    # Track the lookup time for airports from vaCentral
    public const AIRPORT_LOOKUP_TIME = 1;
}
