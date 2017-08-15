<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Transformers\FlightTransformer;

use App\Http\Controllers\AppBaseController;


class FlightController extends AppBaseController
{

    public function get($id)
    {
        $flight = Flight::find($id);
        return fractal($flight, new FlightTransformer())->respond();
    }

    public function search(Request $request)
    {
        $where = [];
        if($request->airline) {
            $airline = Airline::where('code', $request->airline)->first()->id;
            $where['airline_id'] = $airline;
        }

        if($request->depICAO) {
            $airport = Airport::where('icao', $request->depICAO)->first()->id;
            $where['dpt_airport_id'] = $airport;
        }

        if($request->arrICAO) {
            $airport = Airport::where('icao', $request->depICAO)->first()->id;
            $where['dpt_airport_id'] = $airport;
        }

        $flights = Flight::where($where)->get();
        return fractal($flights, new FlightTransformer())->respond();
    }
}
