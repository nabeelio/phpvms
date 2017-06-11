<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Flash;
use Response;

class DashboardAdminController extends AdminBaseController
{
    /**
     * Display a listing of the Airlines.
     */
    public function index(Request $request)
    {
        return view('admin.dashboard');
    }
}
