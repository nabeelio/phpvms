<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Airline
 * @package App\Models
 */
class Airline extends Model
{
    public $table = 'airlines';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'code',
        'name',
        'active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'code' => 'string',
        'name' => 'string',
        'active' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'code' => 'required|max:3|unique:airlines',
        'name' => 'required',
    ];

}
