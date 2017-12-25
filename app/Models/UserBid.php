<?php

namespace App\Models;

/**
 * @package App\Models
 */
class UserBid extends BaseModel
{
    public $table = 'user_bids';

    public $fillable = [
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
