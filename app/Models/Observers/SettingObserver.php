<?php

namespace App\Models\Observers;

use App\Models\Setting;

/**
 * Class SettingObserver
 */
class SettingObserver
{
    /**
     * @param Setting $model
     */
    public function creating(Setting $model): void
    {
        if (!empty($model->id)) {
            $model->id = Setting::formatKey($model->id);
        }
    }
}
