<?php

namespace App\Http\Controllers\Api;

use App\Console\Cron;
use App\Contracts\Controller;
use App\Exceptions\CronInvalid;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Run the cron job from the web
     *
     * @param Request $request
     * @param string  $id      The ID passed in for the cron
     *
     * @return JsonResponse
     */
    public function cron(Request $request, string $id): JsonResponse
    {
        $cron_id = setting('cron.random_id');
        if (empty($cron_id) || $id !== $cron_id) {
            throw new CronInvalid();
        }

        $cron = app(Cron::class);
        $run = $cron->run();

        return response()->json([
            'count' => count($run),
            'tasks' => $run,
        ]);
    }
}
