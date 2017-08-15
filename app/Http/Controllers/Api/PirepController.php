<?php

namespace App\Http\Controllers\Api;

use App\Models\Pirep;
use App\Models\Transformers\PirepTransformer;
use App\Http\Controllers\AppBaseController;


class PirepController extends AppBaseController
{
    public function get($id)
    {
        $pirep = Pirep::find($id);
        return fractal($pirep, new PirepTransformer())->respond();
    }
}
