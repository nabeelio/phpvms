<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @package App\Models
 */
class UserFlight extends Model
{
    public $table = 'user_flights';
    public $timestamps = false;

    public $fillable
        = [
            'user_id',
            'flight_id',
        ];

    /**
     * Relationships
     */
    public function flight()
    {
        return $this->belongsTo('App\Models\Flight', 'flight_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
