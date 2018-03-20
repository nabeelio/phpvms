<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Repositories\PirepRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class DashboardController
 * @package App\Http\Controllers\Frontend
 */
class DashboardController extends Controller
{
    private $pirepRepo;

    /**
     * DashboardController constructor.
     * @param PirepRepository $pirepRepo
     */
    public function __construct(
        PirepRepository $pirepRepo
    ) {
        $this->pirepRepo = $pirepRepo;
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
        } catch (\Exception $e) {
        }

        return view('dashboard.index', [
            'user'       => $user,
            'last_pirep' => $last_pirep,
        ]);
    }
}
