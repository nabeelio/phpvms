<?php

namespace App\Http\Controllers;

use App\Models\User;

class HomeController extends AppBaseController
{
    /**
     * Show the application dashboard.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->take(4)->get();
        return $this->view('home', [
            'users' => $users,
        ]);
    }
}
