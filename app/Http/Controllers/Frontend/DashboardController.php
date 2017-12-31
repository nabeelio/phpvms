<?php

namespace App\Http\Controllers\Frontend;

use Log;
use Illuminate\Support\Facades\Auth;

use App\Repositories\PirepRepository;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;

class DashboardController extends AppBaseController
{
    private $pirepRepo, $userRepo;

    public function __construct(
        PirepRepository $pirepRepo,
        UserRepository $userRepo
    ) {
        $this->pirepRepo = $pirepRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $users = $this->userRepo->recent();
        $pireps = $this->pirepRepo->recent();

        $last_pirep = null;
        $user = Auth::user();

        try {
            $last_pirep = $this->pirepRepo->find($user->last_pirep_id);
        } catch(\Exception $e) { }

        return $this->view('dashboard.index', [
            'user'       => $user,
            'pireps'     => $pireps,
            'users'      => $users,
            'last_pirep' => $last_pirep,
        ]);
    }
}
