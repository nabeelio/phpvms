<?php

namespace App\Repositories;

use App\Models\Acars;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class AcarsRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    public function model()
    {
        return Acars::class;
    }

    public function forPirep($pirep_id)
    {
        return $this->findWhere(['pirep_id' => $pirep_id]);
    }
}
