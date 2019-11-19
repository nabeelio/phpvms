<?php

namespace App\Models;

use App\Contracts\Model;

class PirepFare extends Model
{
    public $table = 'pirep_fares';
    public $timestamps = false;

    protected $fillable = [
        'pirep_id',
        'fare_id',
        'count',
    ];

    protected $casts = [
        'count' => 'integer',
    ];

    public static $rules = [
        'count' => 'required',
    ];

    /**
     * Relationships
     */
    public function fare()
    {
        return $this->belongsTo(Fare::class, 'fare_id');
    }

    public function pirep()
    {
        return $this->belongsTo(Pirep::class, 'pirep_id');
    }
}
