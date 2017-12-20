<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Navdata extends Model
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
        'id' => 'string',
        'type' => 'integer',
    ];
}
