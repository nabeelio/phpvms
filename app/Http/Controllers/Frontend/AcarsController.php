<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Repositories\AcarsRepository;
use App\Services\GeoService;
use Illuminate\Http\Request;

/**
 * Class AcarsController
 * @package App\Http\Controllers\Frontend
 */
class AcarsController extends Controller
{
    private $acarsRepo,
            $geoSvc;

    /**
     * AcarsController constructor.
     * @param AcarsRepository $acarsRepo
     * @param GeoService      $geoSvc
     */
    public function __construct(
        AcarsRepository $acarsRepo,
        GeoService $geoSvc
    ) {
        $this->acarsRepo = $acarsRepo;
        $this->geoSvc = $geoSvc;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $pireps = $this->acarsRepo->getPositions();
        $positions = $this->geoSvc->getFeatureForLiveFlights($pireps);

        return view('acars.index', [
            'pireps'    => $pireps,
            'positions' => $positions,
        ]);
    }
}
