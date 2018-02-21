<?php

namespace App\Http\Controllers\Api;

use App\Repositories\AcarsRepository;
use App\Repositories\PirepRepository;
use App\Services\GeoService;
use Illuminate\Http\Request;


class AcarsController extends RestController
{
    protected $acarsRepo, $geoSvc, $pirepRepo;

    /**
     * AcarsController constructor.
     * @param GeoService $geoSvc
     * @param AcarsRepository $acarsRepo
     * @param PirepRepository $pirepRepo
     */
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
     * @param Request $request
     * @return mixed
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
