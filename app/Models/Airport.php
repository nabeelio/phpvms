<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Airport
 * @package App\Models
 */
class Airport extends Model
{
    use SoftDeletes;

    public $table = 'airports';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'icao'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'icao' => 'required'
    ];

    public function save(array $options = [])
    {
        if(in_array('icao', $options)) {
            $options['icao'] = strtoupper($options['icao']);
        }

        return parent::save($options);
    }
}
