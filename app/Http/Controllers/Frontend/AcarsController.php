<?php

namespace App\Http\Controllers\Frontend;

use App\Repositories\AcarsRepository;
use App\Services\GeoService;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class AcarsController extends Controller
{
    private $acarsRepo, $geoSvc;

    public function __construct(
        AcarsRepository $acarsRepo,
        GeoService $geoSvc
    ) {
        $this->acarsRepo = $acarsRepo;
        $this->geoSvc = $geoSvc;
    }

    /**
     *
     */
    public function index(Request $request)
    {
        $pireps = $this->acarsRepo->getPositions();
        $positions = $this->geoSvc->getFeatureForLiveFlights($pireps);

        return $this->view('acars.index',[
            'pireps' => $pireps,
            'positions' => $positions,
        ]);
    }
}
