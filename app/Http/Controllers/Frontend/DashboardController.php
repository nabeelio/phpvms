<?php

namespace App\Http\Controllers\Frontend;

class DashboardController extends BaseController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('frontend.dashboard');
    }
	public function test()
    {
        return view('frontend.dashboard');
    }
}
