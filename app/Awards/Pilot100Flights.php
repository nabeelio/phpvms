<?php

namespace App\Awards;

use App\Interfaces\AwardInterface;

/**
 * Simple example of an awards class, where you can apply an award when a user
 * has 100 flights. All award classes need to implement the AwardInterface
 * @package App\Awards
 */
class Pilot100Flights extends AwardInterface
{
    public $name = 'Pilot 100 Flights';

    /**
     * If the user has over 100 flights, then we can give them this award
     * @return bool
     */
    public function check(): bool
    {
        return $this->user->flights >= 100;
    }
}
