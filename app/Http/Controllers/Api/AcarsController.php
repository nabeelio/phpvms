<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Services\GeoService;
use App\Repositories\AcarsRepository;
use App\Repositories\PirepRepository;


class AcarsController extends RestController
{
    protected $acarsRepo, $geoSvc, $pirepRepo;

    public function __construct(
        GeoService $geoSvc,
        AcarsRepository $acarsRepo,
        PirepRepository $pirepRepo
    ) {
        $this->geoSvc = $geoSvc;
        $this->acarsRepo = $acarsRepo;
        $this->pirepRepo = $pirepRepo;
    }

    /**
     * Return all of the flights (as points) in GeoJSON format
     */
    public function index(Request $request)
    {
        $pireps = $this->acarsRepo->getPositions();
        $positions = $this->geoSvc->getFeatureForLiveFlights($pireps);

        return response(json_encode($positions), 200, [
            'Content-type' => 'application/json'
        ]);
    }
}
