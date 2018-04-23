<?php

namespace App\Models\Traits;

use App\Interfaces\Model;
use Hashids\Hashids;

trait HashIdTrait
{
    /**
     * @return string
     * @throws \Hashids\HashidsException
     */
    final protected static function createNewHashId(): string
    {
        $hashids = new Hashids('', Model::ID_MAX_LENGTH);
        $mt = str_replace('.', '', microtime(true));
        return $hashids->encode($mt);
    }

    /**
     * Register callbacks
     * @throws \Hashids\HashidsException
     */
    final protected static function bootHashIdTrait(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = static::createNewHashId();
            }
        });
    }
}
