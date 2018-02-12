<?php

namespace App\Models;

class Aircraft extends BaseModel
{
    public $table = 'aircraft';

    public $fillable = [
        'subfleet_id',
        'airport_id',
        'name',
        'icao',
        'registration',
        'zfw',
        'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'zfw'       => 'float',
        'active'    => 'boolean',
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
