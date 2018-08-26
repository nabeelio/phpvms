<?php

namespace App\Interfaces;

use Illuminate\Validation\Validator;

/**
 * Class Repository
 */
abstract class Repository extends \Prettus\Repository\Eloquent\BaseRepository
{
    /**
     * @param       $id
     * @param array $columns
     *
     * @return mixed|null
     */
    public function findWithoutFail($id, array $columns = ['*'])
    {
        try {
            return $this->find($id, $columns);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * @param $values
     *
     * @return bool
     */
    public function validate($values)
    {
        $validator = Validator::make(
            $values,
            $this->model()->rules
        );

        if ($validator->fails()) {
            return $validator->messages();
        }

        return true;
    }

    /**
     * Return N most recent items, sorted by created_at
     *
     * @param int    $count
     * @param string $sort_by created_at (default) or updated_at
     *
     * @return mixed
     */
    public function recent($count = null, $sort_by = 'created_at')
    {
        return $this->orderBy($sort_by, 'desc')->paginate($count);
    }

    /**
     * Find records with a WHERE clause but also sort them
     *
     * @param $where
     * @param $sort_by
     * @param $order_by
     *
     * @return $this
     */
    public function whereOrder($where, $sort_by, $order_by = 'asc')
    {
        return $this->scopeQuery(function ($query) use ($where, $sort_by, $order_by) {
            $q = $query->where($where);
            // See if there are multi-column sorts
            if (\is_array($sort_by)) {
                foreach ($sort_by as $key => $sort) {
                    $q = $q->orderBy($key, $sort);
                }
            } else {
                $q = $q->orderBy($sort_by, $order_by);
            }

            return $q;
        });
    }

    /**
     * Find records where values don't match a list but sort the rest
     *
     * @param string $col
     * @param array  $values
     * @param string $sort_by
     * @param string $order_by
     *
     * @return $this
     */
    public function whereNotInOrder($col, $values, $sort_by, $order_by = 'asc')
    {
        return $this->scopeQuery(function ($query) use ($col, $values, $sort_by, $order_by) {
            $q = $query->whereNotIn($col, $values);
            // See if there are multi-column sorts
            if (\is_array($sort_by)) {
                foreach ($sort_by as $key => $sort) {
                    $q = $q->orderBy($key, $sort);
                }
            } else {
                $q = $q->orderBy($sort_by, $order_by);
            }

            return $q;
        });
    }
}
