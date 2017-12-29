<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;


class StatusController extends AppBaseController
{
    public function status()
    {
        return response()->json(
            ['version' => ''],
            200
        );
    }
}
