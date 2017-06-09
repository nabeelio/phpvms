<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Aircraft
 * @package App\Models
 * @version June 9, 2017, 1:06 am UTC
 */
class Aircraft extends Model
{
    use SoftDeletes;

    public $table = 'aircraft';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'icao',
        'name',
        'full_name',
        'registration',
        'active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'icao' => 'string',
        'name' => 'string',
        'full_name' => 'string',
        'registration' => 'string',
        'active' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'icao' => 'required|max:4',
        'name' => 'required',
        'full_name' => 'required',
        'registration' => 'required',
    ];
}
