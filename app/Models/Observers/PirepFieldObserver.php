<?php

namespace App\Models\Observers;

use App\Models\PirepField;

/**
 * Class PirepFieldObserver
 * @package App\Models\Observers
 */
class PirepFieldObserver
{
    /**
     * @param PirepField $model
     */
    public function creating(PirepField $model): void
    {
        $model->slug = str_slug($model->name);
    }

    /**
     * @param PirepField $model
     */
    public function updating(PirepField $model): void
    {
        $model->slug = str_slug($model->name);
    }
}
