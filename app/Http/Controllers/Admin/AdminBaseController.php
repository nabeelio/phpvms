<?php

namespace App\Http\Controllers\Admin;

use InfyOm\Generator\Utils\ResponseUtil;
use App\Http\Controllers\Controller;
use Response;


class AdminBaseController extends Controller
{
    protected $layout = 'layouts.admin';

    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }
}
