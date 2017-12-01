<?php

namespace App\Http\Controllers\Frontend;

use App\Repositories\PirepRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
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
        $pireps = $this->pirepRepo->recent();
        $users = $this->userRepo->recent();

        return $this->view('dashboard.index', [
            'user' => Auth::user(),
            'pireps' => $pireps,
            'users' => $users,
        ]);
    }
}
