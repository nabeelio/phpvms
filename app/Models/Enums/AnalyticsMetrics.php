<?php

namespace App\Models\Enums;

use App\Interfaces\Enum;

/**
 * Class AnalyticsMetrics
 * @package App\Models\Enums
 */
class AnalyticsMetrics extends Enum
{
    # Track the lookup time for airports from vaCentral
    public const AIRPORT_LOOKUP_TIME = 1;
}
