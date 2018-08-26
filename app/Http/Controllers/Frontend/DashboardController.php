<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Repositories\PirepRepository;
use Illuminate\Support\Facades\Auth;

/**
 * Class DashboardController
 */
class DashboardController extends Controller
{
    private $pirepRepo;

    /**
     * DashboardController constructor.
     *
     * @param PirepRepository $pirepRepo
     */
    public function __construct(PirepRepository $pirepRepo)
    {
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

        // Get the current airport for the weather
        $current_airport = $user->curr_airport_id ?? $user->home_airport_id;

        return view('dashboard.index', [
            'user'            => $user,
            'current_airport' => $current_airport,
            'last_pirep'      => $last_pirep,
        ]);
    }
}
