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
        'id'    => 'string',
        'type'  => 'integer',
        'lat'   => 'float',
        'lon'   => 'float',
    ];
}
