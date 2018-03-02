<?php

namespace App\Models\Traits;

use Hashids\Hashids;

trait HashId
{
    /**
     * @return string
     * @throws \Hashids\HashidsException
     */
    protected static function createNewHashId()
    {
        $hashids = new Hashids('', 12);
        $mt = str_replace('.', '', microtime(true));
        return $hashids->encode($mt);
    }

    /**
     * Register callbacks
     */
    protected static function bootHashId()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = static::createNewHashId();
            }
        });
    }
}
