<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\AppBaseController;
use App\Models\Transformers\FlightTransformer;
use App\Repositories\FlightRepository;


class FlightController extends AppBaseController
{
    protected $flightRepo;

    public function __construct(
        FlightRepository $flightRepo
    ) {
        $this->flightRepo = $flightRepo;
    }

    public function get($id)
    {
        $flight = $this->flightRepo->find($id);
        return fractal($flight, new FlightTransformer())->respond();
    }

    public function search(Request $request)
    {
        $flights = $this->flightRepo->searchCriteria($request)->paginate();
        return fractal($flights, new FlightTransformer())->respond();
    }
}
