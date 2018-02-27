<?php

namespace App\Models;

/**
 * @package App\Models
 */
class Bid extends BaseModel
{
    public $table = 'bids';

    public $fillable = [
        'user_id',
        'flight_id',
    ];

    /**
     * Relationships
     */
    public function flight()
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
