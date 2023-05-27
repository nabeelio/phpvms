<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LiveMapController extends Controller
{
    /**
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request): View
    {
        return view('livemap.index');
    }
}
