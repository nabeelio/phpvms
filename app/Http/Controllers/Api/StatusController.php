<?php

namespace App\Http\Controllers\Api;

use PragmaRX\Version\Package\Facade as Version;

class StatusController extends RestController
{
    public function status()
    {
        return response()->json([
            'version' => Version::compact(),
            'php' => PHP_VERSION,
        ]);
    }
}
