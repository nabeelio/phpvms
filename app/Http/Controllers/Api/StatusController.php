<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use PragmaRX\Version\Package\Facade as Version;

/**
 * Class StatusController
 */
class StatusController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        return response()->json([
            'version' => Version::compact(),
            'php'     => PHP_VERSION,
        ]);
    }
}
