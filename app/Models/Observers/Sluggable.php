<?php

namespace App\Models\Observers;

/**
 * Create a slug from a name
 *
 * @property object attributes
 */
class Sluggable
{
    /**
     * @param $model
     */
    public function creating($model): void
    {
        $model->slug = str_slug($model->name);
    }

    /**
     * @param $model
     */
    public function updating($model): void
    {
        $model->slug = str_slug($model->name);
    }

    /**
     * @param $name
     */
    public function setNameAttribute($name): void
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = str_slug($name);
    }
}
