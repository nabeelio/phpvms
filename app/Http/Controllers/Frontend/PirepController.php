<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AppBaseController;

use App\Models\Pirep;


class PirepController extends AppBaseController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $pireps = Pirep::where('user_id', $user->id)
                       ->orderBy('created_at', 'desc')
                       ->get();

        return $this->view('pireps.index', [
            'user' => $user,
            'pireps' => $pireps,
        ]);
    }

    public function create()
    {
        return view('pireps.create');
    }
}
