<?php

namespace App\Http\Controllers\Api;

use App\Repositories\AirportRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\Airport as AirportResource;

use VaCentral\Airport as AirportLookup;

class AirportController extends AppBaseController
{
    protected $airportRepo;

    public function __construct(
        AirportRepository $airportRepo
    ) {
        $this->airportRepo = $airportRepo;
    }

    /**
     * Do a lookup, via vaCentral, for the airport information
     * @param $id
     * @return AirportResource
     */
    public function lookup($id)
    {
        $airport = AirportLookup::get($id);
        return new AirportResource(collect($airport));
    }
}
