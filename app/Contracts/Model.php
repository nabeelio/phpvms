<?php

namespace App\Contracts;

/**
 * Class Model
 *
 * @property mixed $id
 * @property bool  $skip_mutator
 *
 * @method static where(array $array)
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
