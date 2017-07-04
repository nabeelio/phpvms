<?php

namespace App\Services;

use App\Models\AircraftClass;
use Dompdf\Exception;

class AircraftService extends BaseService
{

    public function create(
        array $attributes
    ) {

        $repo = app('App\Repositories\SubfleetRepository');
        try {
            $model = $repo->create($attributes);
        } catch (Exception $e) {
            return false;
        }

        /*if ($class != null) {
            $model->class()->associate($class);
            $model->save();
        }*/

        return $model;
    }
}
