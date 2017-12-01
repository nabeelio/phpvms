<?php

namespace App\Repositories;

use Illuminate\Validation\Validator;


abstract class BaseRepository extends \Prettus\Repository\Eloquent\BaseRepository {

    /**
     * @param $id
     * @param array $columns
     * @return mixed|void
     */
    public function findWithoutFail($id, $columns = ['*'])
    {
        try {
            return $this->find($id, $columns);
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * @param $values
     * @return bool
     */
    public function validate($values)
    {
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
