<?php

namespace App\Models\Observers;

use App\Models\Subfleet;

/**
 * Class SubfleetObserver
 * @package App\Models\Observers
 */
class SubfleetObserver
{
    /**
     * @param Subfleet $model
     */
    public function creating(Subfleet $model)
    {
        if (!filled($model->ground_handling_multiplier)) {
            $model->ground_handling_multiplier = 100;
        }
    }
}
