<?php

namespace Modules\Installer\Utils;

use Illuminate\Support\Facades\Log;

trait LoggerTrait
{
    protected function comment($text)
    {
        Log::info($text);
    }

    protected function info($text)
    {
        Log::info($text);
    }

    protected function error($text)
    {
        Log::error($text);
    }
}
