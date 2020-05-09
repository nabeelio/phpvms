<?php

namespace App\Models\Enums;

use App\Contracts\Enum;

class PageType extends Enum
{
    public const HTML = 0;
    public const MARKDOWN = 1;
}
