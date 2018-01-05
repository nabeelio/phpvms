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

    /**
     * Return N most recent items, sorted by created_at
     * @param int $count
     * @param string $sort_by created_at (default) or updated_at
     * @return mixed
     */
    public function recent($count = 5, $sort_by = 'created_at')
    {
        return $this->orderBy($sort_by, 'desc')->paginate($count);
    }

    /**
     * Find records with a WHERE clause but also sort them
     * @param $where
     * @param $sort_by
     * @param $order_by
     * @return $this
     */
    public function whereOrder($where, $sort_by, $order_by)
    {
        return $this->scopeQuery(function($query) use ($where, $sort_by, $order_by) {
            return $query->where($where)->orderBy($sort_by, $order_by);
        });
    }
}
