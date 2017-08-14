<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use App\Http\Controllers\AppBaseController;


class BaseController extends AppBaseController
{
    public function status()
    {
        return response()->json(
            ['message' => 'hi'],
            200
        );
    }
}
