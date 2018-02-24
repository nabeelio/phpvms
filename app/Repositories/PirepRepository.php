<?php

namespace App\Repositories;

use App\Models\Enums\PirepState;
use App\Models\Pirep;
use App\Models\User;

class PirepRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'user_id',
        'status',
        'state',
    ];

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

    /**
     * Number of PIREPs that are pending
     * @param User|null $user
     * @return mixed
     */
    public function getPendingCount(User $user = null)
    {
        $where = [
            'state' => PirepState::PENDING,
        ];

        if ($user !== null) {
            $where['user_id'] = $user->id;
        }

        $pireps = $this->orderBy('created_at', 'desc')
                       ->findWhere($where, ['id'])
                       ->count();
        return $pireps;
    }
}
