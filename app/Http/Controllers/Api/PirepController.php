<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Pirep as PirepResource;
use App\Http\Controllers\AppBaseController;
use App\Repositories\PirepRepository;


class PirepController extends AppBaseController
{
    protected $pirepRepo;

    public function __construct(PirepRepository $pirepRepo)
    {
        $this->pirepRepo = $pirepRepo;
    }

    public function get($id)
    {
        PirepResource::withoutWrapping();
        return new PirepResource($this->pirepRepo->find($id));
    }
}
