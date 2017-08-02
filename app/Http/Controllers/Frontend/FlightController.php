<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Repositories\FlightRepository;
use App\Http\Controllers\AppBaseController;


class FlightController extends AppBaseController
{
    private $flightRepo;

    public function __construct(FlightRepository $flightRepo)
    {
        $this->flightRepo = $flightRepo;
    }

    public function index(Request $request)
    {
        $flights = $this->flightRepo->findByField('active', true);

        return $this->view('flights.index', [
            'flights' => $flights,
        ]);
    }

    public function show($id)
    {

    }

    public function update()
    {

    }
}
