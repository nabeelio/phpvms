<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\CanResetPassword;

/**
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $api_key
 * @property string $flights
 * @property string $flight_time
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Illuminate\Notifications\Notifiable
 * @mixin \Laratrust\Traits\LaratrustUserTrait
 */
class User extends Authenticatable
{
    use Notifiable;
    use LaratrustUserTrait;
    //use SoftDeletes;

    public $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'airline_id',
        'home_airport_id',
        'curr_airport_id',
        'rank_id',
        'timezone',
        'state',
        'status',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'flights'       => 'integer',
        'flight_time'   => 'integer',
        'balance'       => 'double',
        'state'         => 'integer',
        'status'        => 'integer',
    ];

    public static $rules = [

    ];

    public function getPilotIdAttribute($value)
    {
        $length = setting('pilots.id_length');
        return $this->airline->icao . str_pad($this->id, $length, '0', STR_PAD_LEFT);
    }

    public function getGravatarAttribute($value)
    {
        $size = 80;
        $default = 'https://en.gravatar.com/userimage/12856995/7c7c1da6387853fea65ff74983055386.png';
        return "https://www.gravatar.com/avatar/" .
                md5( strtolower( trim( $this->email) ) ) . "?d=" . urlencode( $default ) . "&s=" . $size;
    }

    /**
     * Foreign Keys
     */

    public function airline()
    {
        return $this->belongsTo('App\Models\Airline', 'airline_id');
    }

    public function home_airport()
    {
        return $this->belongsTo('App\Models\Airport', 'home_airport_id');
    }

    public function current_airport()
    {
        return $this->belongsTo('App\Models\Airport', 'curr_airport_id');
    }

    public function bids()
    {
        return $this->hasMany('App\Models\UserBid', 'user_id');
    }

    public function pireps()
    {
        return $this->hasMany('App\Models\Pirep', 'user_id');
    }

    public function rank()
    {
        return $this->belongsTo('App\Models\Rank', 'rank_id');
    }
}
