<?php

namespace App\Repositories;

use App\Models\Pirep;
use App\Models\User;

class PirepRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Pirep::class;
    }

    /**
     * Get all the pending reports in order. Returns the Pirep
     * model but you still need to call ->all() or ->paginate()
     * @param User|null $user
     * @return Pirep
     */
    public function getPending(User $user=null)
    {
        $where = [];
        if($user !== null) {
            $where['user_id'] = $user->id;
        }

        $pireps = $this->orderBy('created_at', 'desc')->findWhere($where)->all();
        return $pireps;
    }
}
