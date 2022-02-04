<?php

namespace App\Models;

use App\Contracts\Model;
use Carbon\Carbon;

/**
 * @property int    user_id
 * @property string flight_id
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Flight flight
 * @property User   user
 */
class Bid extends Model
{
    public $table = 'bids';

    protected $fillable = [
        'user_id',
        'flight_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
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
