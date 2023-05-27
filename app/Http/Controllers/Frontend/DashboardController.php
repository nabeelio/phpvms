<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Repositories\PirepRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class DashboardController
 */
class DashboardController extends Controller
{
    /**
     * DashboardController constructor.
     *
     * @param PirepRepository $pirepRepo
     */
    public function __construct(
        private readonly PirepRepository $pirepRepo
    ) {
    }

    /**
     * Show the application dashboard.
     *
     * @return View
     */
    public function index(): View
    {
        $last_pirep = null;
        $with_pirep = ['aircraft', 'arr_airport', 'comments', 'dpt_airport'];

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->loadMissing('journal');

        try {
            $last_pirep = $this->pirepRepo->with($with_pirep)->find($user->last_pirep_id);
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
