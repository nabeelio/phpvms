<?php

namespace App\Models;

class Navdata extends BaseModel
{
    public $table = 'navdata';
    public $timestamps = false;
    public $incrementing = false;

    public $fillable = [
        'id',
        'name',
        'type',
        'lat',
        'lon',
        'freq',
    ];

    public $casts = [
        'type'  => 'integer',
        'lat'   => 'float',
        'lon'   => 'float',
        'freq'  => 'float',
    ];

    protected static function boot()
    {
        parent::boot();

        /**
         * Make sure the ID is all caps
         */
        static::creating(function (Navdata $model) {
            if (!empty($model->id)) {
                $model->id = strtoupper($model->id);
            }
        });
    }
}
