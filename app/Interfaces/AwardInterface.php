<?php

namespace App\Interfaces;

use App\Models\Award;
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
     * @var User
     */
    protected $user;

    /**
     * AwardInterface constructor.
     * @param Award $award
     * @param User $user
     */
    public function __construct(Award $award, User $user)
    {
        $this->award = $award;
        $this->user = $user;
    }

    /**
     * Add the award to this user, if they don't already have it
     * @return bool|UserAward
     */
    public function addAward()
    {
        $w = [
            'user_id' => $this->user->id,
            'award_id' => $this->award->id
        ];

        $found = UserAward::where($w)->count('id');
        if ($found > 0) {
            return true;
        }

        // Associate this award to the user now
        $award = new UserAward($w);
        $award->save();

        return $award;
    }

    /**
     * Each award class just needs to return true or false
     * if it should actually be awarded to a user
     * @return boolean
     */
    public function check()
    {
        return false;
    }
}
