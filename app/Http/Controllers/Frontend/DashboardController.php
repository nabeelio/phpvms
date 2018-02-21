<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Repositories\PirepRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    private $pirepRepo, $userRepo;

    /**
     * DashboardController constructor.
     * @param PirepRepository $pirepRepo
     * @param UserRepository $userRepo
     */
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
        $last_pirep = null;
        $user = Auth::user();

        try {
            $last_pirep = $this->pirepRepo->find($user->last_pirep_id);
        } catch(\Exception $e) { }

        return $this->view('dashboard.index', [
            'user'       => $user,
            'last_pirep' => $last_pirep,
        ]);
    }
}
