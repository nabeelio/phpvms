<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Controller;
use App\Services\VersionService;

/**
 * Class StatusController
 */
class StatusController extends Controller
{
    private VersionService $versionSvc;

    public function __construct(VersionService $versionSvc)
    {
        $this->versionSvc = $versionSvc;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function status()
    {
        return response()->json([
            'version' => $this->versionSvc->getCurrentVersion(true),
            'php'     => PHP_VERSION,
        ]);
    }
}
