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
        'rank_id',
        'api_key',
        'country',
        'home_airport_id',
        'curr_airport_id',
        'last_pirep_id',
        'flights',
        'flight_time',
        'transferred_time',
        'balance',
        'timezone',
        'state',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'api_key',
        'password',
        'remember_token',
    ];

    protected $casts = [
        'flights'           => 'integer',
        'flight_time'       => 'integer',
        'transferred_time'  => 'integer',
        'balance'           => 'double',
        'state'             => 'integer',
        'status'            => 'integer',
    ];

    public static $rules = [
        'name' => 'required',
        'email' => 'required|email',
    ];

    /**
     * @return string
     */
    public function getPilotIdAttribute()
    {
        $length = setting('pilots.id_length');
        return $this->airline->icao . str_pad($this->id, $length, '0', STR_PAD_LEFT);
    }

    /**
     * @return string
     */
    public function getIdentAttribute()
    {
        return $this->getPilotIdAttribute();
    }

    /**
     * Return the timezone
     * @return mixed
     */
    public function getTzAttribute()
    {
        return $this->timezone;
    }

    /**
     * @param mixed $size Size of the gravatar, in pixels
     * @return string
     */
    public function gravatar($size=null)
    {
        $default = config('gravatar.default');

        $uri = config('gravatar.url')
             . md5(strtolower(trim($this->email))).'?d='.urlencode($default);

        if($size !== null) {
            $uri .= '&s='.$size;
        }

        return $uri;
    }

    /**
     * Foreign Keys
     */

    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    public function home_airport()
    {
        return $this->belongsTo(Airport::class, 'home_airport_id');
    }

    public function current_airport()
    {
        return $this->belongsTo(Airport::class, 'curr_airport_id');
    }

    public function last_pirep()
    {
        return $this->belongsTo(Pirep::class, 'last_pirep_id');
    }

    public function bids()
    {
        return $this->hasMany(UserBid::class, 'user_id');
    }

    public function pireps()
    {
        return $this->hasMany(Pirep::class, 'user_id');
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class, 'rank_id');
    }
}
