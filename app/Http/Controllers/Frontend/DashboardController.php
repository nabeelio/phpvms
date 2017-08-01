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
        return $this->view('frontend/dashboard');
    }
	public function test()
    {
        return $this->view('frontend/dashboard');
    }
}
