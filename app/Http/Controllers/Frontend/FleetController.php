<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Http\Requests\CreateSubfleetRequest;
use App\Http\Requests\UpdateSubfleetRequest;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Enums\FareType;
use App\Models\Enums\FuelType;
use App\Models\Enums\ImportExportType;
use App\Models\Expense;
use App\Models\Subfleet;
use App\Repositories\AircraftRepository;
use App\Repositories\FareRepository;
use App\Repositories\RankRepository;
use App\Repositories\SubfleetRepository;
use App\Services\ExportService;
use App\Services\FareService;
use App\Services\FleetService;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Prettus\Repository\Criteria\RequestCriteria;

class FleetController extends Controller
{

    private $aircraftRepo;

    /**
     * SubfleetController constructor.
     *
     * @param AircraftRepository $aircraftRepo
     */
    public function __construct(
        AircraftRepository $aircraftRepo
    ) {
        $this->aircraftRepo = $aircraftRepo;
    }

    public function showFleet()
    {
        $w = [];
        $aircraft = $this->aircraftRepo->with(['subfleet'])->whereOrder($w, 'registration', 'asc');
        $aircraft = $aircraft->all();
        
        return view('flights.fleet', [
            'aircraft'    => $aircraft
        ]);
    }

}
