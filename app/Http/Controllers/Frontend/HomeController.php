<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        try {
            $users = User::orderBy('created_at', 'desc')->take(4)->get();
        } catch (QueryException $e) {
            return view('system/errors/not_installed');
        }

        return $this->view('home', [
            'users' => $users,
        ]);
    }
}
