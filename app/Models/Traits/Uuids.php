<?php

namespace App\Models\Traits;

use Webpatser\Uuid\Uuid;

trait Uuids
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $key = $model->getKeyName();
            if (empty($model->{$key})) {
                $model->{$key} = Uuid::generate()->string;
            }
            #$model->{$model->getKeyName()} = Uuid::generate()->string;
        });
    }
}
