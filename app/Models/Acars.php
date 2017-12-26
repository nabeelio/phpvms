<?php

namespace App\Models;

use App\Models\Traits\HashId;

class Acars extends BaseModel
{
    use HashId;
    public $incrementing = false;

    public $table = 'acars';

    public $fillable = [
        'pirep_id',
        'name',
        'lat',
        'lon',
        'altitude',
        'vs',
        'gs',
        'transponder',
        'autopilot',
        'fuel_flow',
        'sim_time',
    ];

    public $casts = [
        'lat'       => 'float',
        'lon'       => 'float',
        'heading'   => 'integer',
        'altitude'  => 'integer',
        'vs'        => 'float',
        'gs'        => 'float',
        'fuel_flow' => 'float',
    ];

    /**
     * FKs
     */

    public function pirep()
    {
        return $this->belongsTo('App\Models\Pirep', 'pirep_id');
    }
}
