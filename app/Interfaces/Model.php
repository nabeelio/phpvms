<?php

namespace App\Interfaces;

/**
 * Class Model
 * @property mixed $id
 * @package App\Interfaces
 */
abstract class Model extends \Illuminate\Database\Eloquent\Model
{
    public const ID_MAX_LENGTH = 12;
}
