<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AppBaseController;

use App\Models\Pirep;
use App\Models\User;


class DashboardController extends AppBaseController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pireps = Pirep::orderBy('created_at', 'desc')->take(5)->get();
        $users = User::orderBy('created_at', 'desc')->take(5)->get();

        return $this->view('dashboard.index', [
            'user' => Auth::user(),
            'pireps' => $pireps,
            'users' => $users,
        ]);
    }
}
