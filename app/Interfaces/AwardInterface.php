<?php

namespace App\Interfaces;

use App\Models\User;
use App\Models\UserAward;

/**
 * Base class for the Awards, they need to extend this
 * @package App\Interfaces
 */
class AwardInterface
{
    /**
     * @var string The name of the award class, needs to be set
     */
    public $name = '';

    /**
     * @var Award
     */
    protected $award;

    /**
     * AwardInterface constructor.
     * @param $award
     */
    public function __construct($award)
    {
        $this->award = $award;
    }

    /**
     * Add the award to this user, if they don't already have it
     * @param User $user
     * @return bool
     */
    public function addAward(User $user)
    {
        $w = [
            'user_id' => $user->id,
            'award_id' => $this->award->id
        ];

        $found = UserAward::where($w)->count('id');
        if($found > 0) {
            return true;
        }

        // Associate this award to the user now
        $award = new UserAward($w);
        $award->save();
    }

    /**
     * Each award class just needs to award
     * @param User $user
     * @return mixed
     */
    public function check(User $user)
    {
        return false;
    }
}
