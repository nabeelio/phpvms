<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Enums\PirepState;
use App\Repositories\AircraftRepository;
use App\Repositories\PirepRepository;
use App\Repositories\SubfleetRepository;
use Illuminate\Http\Request;

class SubfleetController extends Controller
{
    private $aircraftRepo;
    private $pirepRepo;
    private $subfleetRepo;

    public function __construct(
        AircraftRepository $aircraftRepo,
        PirepRepository $pirepRepo,
        SubfleetRepository $subfleetRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
        $this->pirepRepo = $pirepRepo;
        $this->subfleetRepo = $subfleetRepo;
    }

    public function index(Request $request)
    {
        $subfleets = $this->subfleetRepo->orderBy('airline_id')->orderBy('name')
            ->with(['airline', 'aircraft'])
            ->get();

        return view('subfleets.index', [
            'subfleets' => $subfleets,
        ]);
    }

    public function subfleet($id)
    {
        $subfleet = $this->subfleetRepo->find($id);
        if (!$subfleet) {
            return redirect(route('frontend.subfleets.index'));
        }

        return view('subfleets.subfleet', [
            'subfleet' => $subfleet,
        ]);
    }

    public function aircraft($id)
    {
        $aircraft = $this->aircraftRepo->find($id);
        if (!$aircraft) {
            return redirect(route('frontend.subfleets.index'));
        }

        $pireps = $this->pirepRepo->where('aircraft_id', $id)->where('state', PirepState::ACCEPTED)
            ->orderby('submitted_at', 'desc')
            ->take(5)
            ->get();

        return view('subfleets.aircraft', [
            'aircraft' => $aircraft,
            'pireps'   => $pireps,
        ]);
    }
}
