<?php

namespace App\Models;

use App\Interfaces\Model;

/**
 * @package App\Models
 */
class Bid extends Model
{
    public $table = 'bids';

    protected $fillable = [
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
