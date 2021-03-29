<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Enums\ActiveState;
use App\Repositories\AirlineRepository;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    private $airlineRepo;

    public function __construct(
        AirlineRepository $airlineRepo
    ) {
        $this->airlineRepo = $airlineRepo;
    }

    // Display All Active Airlines
    public function index(Request $request)
    {
        $airlines = $this->airlineRepo->where('active', ActiveState::ACTIVE)
            ->orderBy('name')
            ->get();

        if ($airlines->count() === 1) {
            return redirect(route('frontend.airlines.airline', [$airlines->first()->id]));
        }

        return view('airlines.index', [
            'airlines' => $airlines,
        ]);
    }

    // Display Only Selected Airline
    public function airline($id)
    {
        $airline = $this->airlineRepo->find($id);

        return view('airlines.airline', [
            'airline' => $airline,
        ]);
    }
}
