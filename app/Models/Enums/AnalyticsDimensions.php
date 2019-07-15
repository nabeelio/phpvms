<?php

namespace App\Models\Enums;

use App\Contracts\Enum;

/**
 * Class AnalyticsDimensions
 */
class AnalyticsDimensions extends Enum
{
    public const PHP_VERSION = 1;
    public const DATABASE_VERSION = 2;
    public const PHPVMS_VERSION = 3;
}
