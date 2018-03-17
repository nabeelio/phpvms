<?php

namespace Modules\Sample\Http\Controllers\Api;

use App\Http\Controllers\Api\RestController;
use Illuminate\Http\Request;

class SampleController extends RestController
{
    /**
     * Just send out a message
     * @param Request $request
     */
    public function index(Request $request)
    {
        return $this->message('Hello, world!');
    }

    /**
     * @param Request $request
     */
    public function hello(Request $request)
    {
        // Another way to return JSON, this for a custom response
        // It's recommended to use Resources for responses from the database
        return response()->json([
            'name' => Auth::user()->name,
        ]);
    }

}
