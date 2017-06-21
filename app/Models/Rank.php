<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Ranking
 * @package App\Models
 */
class Rank extends Model
{

    public $table = 'ranks';

    public $fillable = [
        'name',
        'hours',
        'auto_approve_acars',
        'auto_approve_manual',
        'auto_promote'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'hours' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
}
