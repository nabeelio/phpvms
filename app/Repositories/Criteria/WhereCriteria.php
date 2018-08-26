<?php

namespace App\Repositories\Criteria;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class RequestCriteria
 */
class WhereCriteria implements CriteriaInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;
    protected $where;

    public function __construct($request, $where)
    {
        $this->request = $request;
        $this->where = $where;
    }

    /**
     * Apply criteria in query repository
     *
     * @param Builder|Model       $model
     * @param RepositoryInterface $repository
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        if ($this->where) {
            $model = $model->where($this->where);
        }

        return $model;
    }
}
