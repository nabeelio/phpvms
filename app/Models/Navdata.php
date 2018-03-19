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

    /**
     * Make sure the ID is in all caps
     * @param $id
     */
    public function setIdAttribute($id): void
    {
        $this->attributes['id'] = strtoupper($id);
    }
}
