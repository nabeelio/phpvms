<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Aircraft;
use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Models\Subfleet;
use Illuminate\Http\Request;

class SubfleetController extends Controller
{
    public function index(Request $request)
    {
        $subfleets = Subfleet::orderBy('airline_id')->orderBy('name')
            ->with(['airline', 'aircraft'])
            ->get();

        return view('subfleets.index', [
            'subfleets' => $subfleets,
        ]);
    }

    public function subfleet($id)
    {
        $subfleet = Subfleet::find($id);
        if (!$subfleet) {
            return redirect(route('frontend.subfleets.index'));
        }

        return view('subfleets.subfleet', [
            'subfleet' => $subfleet,
        ]);
    }

    public function aircraft($id)
    {
        $aircraft = Aircraft::find($id);
        if (!$aircraft) {
            return redirect(route('frontend.subfleets.index'));
        }

        $pireps = Pirep::where('aircraft_id', $id)->where('state', PirepState::ACCEPTED)
            ->orderby('submitted_at', 'desc')
            ->take(5)
            ->get();

        return view('subfleets.aircraft', [
            'aircraft' => $aircraft,
            'pireps'   => $pireps,
        ]);
    }
}
