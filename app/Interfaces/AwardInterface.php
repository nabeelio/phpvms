<?php

namespace App\Interfaces;

use App\Facades\Utils;
use App\Models\Award;
use App\Models\User;
use App\Models\UserAward;

/**
 * Base class for the Awards, they need to extend this
 * @package App\Interfaces
 */
abstract class AwardInterface
{
    public $name = '';
    public $param_description = '';

    protected $award;
    protected $user;

    /**
     * Each award class just needs to return true or false if it should actually
     * be awarded to a user. This is the only method that needs to be implemented
     * @param null $params Optional parameters that are passed in from the UI
     * @return bool
     */
    abstract public function check($params = null): bool;

    /**
     * AwardInterface constructor.
     * @param Award $award
     * @param User $user
     */
    public function __construct(Award $award = null, User $user = null)
    {
        $this->award = $award;
        $this->user = $user;
    }

    /**
     * Run the main handler for this award class to determine if
     * it should be awarded or not
     */
    public function handle()
    {
        # Check if the params are a JSON object or array
        $param = $this->award->ref_class_params;
        if ($this->award->ref_class_params && Utils::isObject($this->award->ref_class_params)) {
            $param = json_decode($this->award->ref_class_params);
        }

        if ($this->check($param)) {
            $this->addAward();
        }
    }

    /**
     * Add the award to this user, if they don't already have it
     * @return bool|UserAward
     */
    protected function addAward()
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
}
