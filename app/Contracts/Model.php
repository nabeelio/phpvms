<?php

namespace App\Contracts;

/**
 * @property mixed $id
 * @property bool  $skip_mutator
 *
 * @method static create(array $attrs)
 * @method static Model find(int $id)
 * @method static Model where(array $array)
 * @method static Model firstOrCreate(array $where, array $array)
 * @method static Model updateOrCreate(array $array, array $attrs)
 * @method static truncate()
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    public const ID_MAX_LENGTH = 12;

    /**
     * For the factories, skip the mutators. Only apply to one instance
     *
     * @var bool
     */
    public $skip_mutator = false;
}
