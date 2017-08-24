<?php

namespace App\Repositories;

use Illuminate\Validation\Validator;


abstract class BaseRepository extends \Prettus\Repository\Eloquent\BaseRepository {

    public function validate($values) {
        $validator = Validator::make(
            $values,
            $this->model()->rules
        );

        if($validator->fails()) {
            return $validator->messages();
        }

        return true;
    }
}
