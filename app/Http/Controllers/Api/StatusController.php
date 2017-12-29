<?php

namespace App\Http\Controllers\Api;

use PragmaRX\Version\Package\Facade as Version;
use App\Http\Controllers\AppBaseController;


class StatusController extends AppBaseController
{
    public function status()
    {
        return response()->json([
            'version' => Version::compact(),
            'php' => PHP_VERSION,
        ]);
    }
}
