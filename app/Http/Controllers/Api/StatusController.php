<?php

namespace App\Http\Controllers\Api;

use PragmaRX\Version\Package\Facade as Version;

class StatusController extends RestController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        return response()->json([
            'version' => Version::compact(),
            'php' => PHP_VERSION,
        ]);
    }
}
