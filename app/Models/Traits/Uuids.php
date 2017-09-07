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
                $hashids = new Hashids('', 10);

                $mt = str_replace('.', '', microtime(true));
                
                $id = $hashids->encode($mt);
                $model->{$key} = $id;
            }
        });
    }
}
