<?php
namespace App\Repositories;

use App\Models\Enums\PilotState;
use App\Models\User;
use App\Repositories\Traits\CacheableRepository;
use Prettus\Repository\Contracts\CacheableInterface;

class UserRepository extends BaseRepository implements CacheableInterface
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name' => 'like',
        'email' => 'like',
        'home_airport_id',
        'curr_airport_id',
        'state'
    ];

    public function model()
    {
        return User::class;
    }

    /**
     * Number of PIREPs that are pending
     * @return mixed
     */
    public function getPendingCount()
    {
        $where = [
            'state' => PilotState::PENDING,
        ];

        $users = $this->orderBy('created_at', 'desc')->findWhere($where)->count();
        return $users;
    }
}
