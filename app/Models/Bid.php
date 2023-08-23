<?php

namespace App\Models;

use App\Contracts\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int        user_id
 * @property string     flight_id
 * @property int        aircraft_id
 * @property Carbon     created_at
 * @property Carbon     updated_at
 * @property Aircraft   aircraft
 * @property Flight     flight
 * @property User       user
 */
class Bid extends Model
{
    public $table = 'bids';

    protected $fillable = [
        'user_id',
        'flight_id',
        'aircraft_id',
    ];

    protected $casts = [
        'user_id'     => 'integer',
        'aircraft_id' => 'integer',
    ];

    /**
     * Relationships
     */
    public function aircraft(): BelongsTo
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id');
    }

    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
