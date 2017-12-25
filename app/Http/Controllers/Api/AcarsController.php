<?php

namespace App\Http\Controllers\Api;

use Log;
use App\Models\Acars;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

use App\Repositories\AcarsRepository;
use App\Repositories\PirepRepository;

use App\Http\Resources\Acars as AcarsResource;


class AcarsController extends AppBaseController
{
    protected $acarsRepo, $pirepRepo;

    public function __construct(
        AcarsRepository $acarsRepo,
        PirepRepository $pirepRepo
    ) {
        $this->acarsRepo = $acarsRepo;
        $this->pirepRepo = $pirepRepo;
    }

    public function index(Request $request)
    {
        /*PirepResource::withoutWrapping();
        return new PirepResource($this->pirepRepo->find($id));*/
    }

    /**
     * Return the current ACARS map data in GeoJSON format
     * @param Request $request
     */
    public function geojson(Request $request)
    {

    }

}
