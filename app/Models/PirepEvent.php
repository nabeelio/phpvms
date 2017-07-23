<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class PirepEvent
 *
 * @package App\Models
 */
class PirepEvent extends Model
{
    public $table = 'pirep_fields';

    public $fillable
        = [
            'name',
            'value',
        ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts
        = [
            'name'     => 'string',
            'required' => 'integer',
        ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules
        = [
            'name' => 'required',
        ];

    public function pirep()
    {
        return $this->belongsTo('App\Models\Pirep', 'pirep_id');
    }
}
