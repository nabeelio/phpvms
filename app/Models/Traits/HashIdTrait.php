<?php

namespace App\Models\Traits;

use Hashids\Hashids;

trait HashIdTrait
{
    /**
     * @return string
     * @throws \Hashids\HashidsException
     */
    protected static function createNewHashId(): string
    {
        $hashids = new Hashids('', 12);
        $mt = str_replace('.', '', microtime(true));

        return $hashids->encode($mt);
    }

    /**
     * Register callbacks
     * @throws \Hashids\HashidsException
     */
    protected static function bootHashIdTrait()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = static::createNewHashId();
            }
        });
    }
}
