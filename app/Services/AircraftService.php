<?php

namespace App\Services;

use App\Models\Aircraft;
use App\Models\AircraftClass;
use Dompdf\Exception;

class AircraftService extends BaseService
{

    public function create(
        array $attributes,
        AircraftClass $class = null
    ) {

        $repo = app('App\Repositories\AircraftRepository');
        try {
            $model = $repo->create($attributes);
        } catch (Exception $e) {
            return false;
        }

        if ($class != null) {
            $model->class()->associate($class);
            $model->save();
        }

        return $model;
    }
}
