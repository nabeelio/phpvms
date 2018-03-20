<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\Controller;
use App\Repositories\AcarsRepository;
use App\Services\GeoService;
use Illuminate\Http\Request;

/**
 * Class AcarsController
 * @package App\Http\Controllers\Api
 */
class AcarsController extends Controller
{
    private $acarsRepo,
            $geoSvc;

    /**
     * AcarsController constructor.
     * @param GeoService      $geoSvc
     * @param AcarsRepository $acarsRepo
     */
    public function __construct(
        GeoService $geoSvc,
        AcarsRepository $acarsRepo
    ) {
        $this->geoSvc = $geoSvc;
        $this->acarsRepo = $acarsRepo;
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
