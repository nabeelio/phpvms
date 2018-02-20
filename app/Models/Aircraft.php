<?php

namespace App\Models;

use App\Support\ICAO;

class Aircraft extends BaseModel
{
    public $table = 'aircraft';

    public $fillable = [
        'subfleet_id',
        'airport_id',
        'name',
        'icao',
        'registration',
        'hex_code',
        'zfw',
        'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'subfleet_id'   => 'integer',
        'zfw'           => 'float',
        'active'        => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'subfleet_id' => 'required',
        'name'         => 'required',
    ];

    /**
     * Callbacks
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function (Aircraft $model) {
            if (!empty($model->icao)) {
                $model->icao = strtoupper(trim($model->icao));
            }

            if(empty($model->hex_code)) {
                $model->hex_code = ICAO::createHexCode();
            }
        });
    }

    /**
     * foreign keys
     */

    public function airport()
    {
        return $this->belongsTo(Airport::class, 'airport_id');
    }

    public function subfleet()
    {
        return $this->belongsTo(Subfleet::class, 'subfleet_id');
    }
}
