<?php

namespace App\Models\Traits;

use Hashids\Hashids;


trait Uuids
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $key = $model->getKeyName();

            if (empty($model->{$key})) {
                $hashids = new Hashids('', 8);
                $id = $hashids->encode((int)microtime(true));
                $model->{$key} = $id;
            }
        });
    }
}
