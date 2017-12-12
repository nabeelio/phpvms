<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\CanResetPassword;

/**
 * App\User
 *
 * @property integer
 *                                                       $id
 * @property string
 *                                                       $name
 * @property string
 *                                                       $email
 * @property string
 *                                                       $password
 * @property string
 *                                                       $remember_token
 * @property \Carbon\Carbon
 *                                                       $created_at
 * @property \Carbon\Carbon
 *                                                       $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[]
 *                $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[]
 *                $unreadNotifications
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User
 *         whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User
 *         whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User
 *         whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User
 *         wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User
 *         whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User
 *         whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User
 *         whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use Notifiable;
    use EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable
        = [
            'name',
            'email',
            'password',
            'airline_id',
            'home_airport_id',
            'curr_airport_id',
            'rank_id',
            'timezone',
            'active',
        ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'flights'       => 'integer',
        'flight_time'   => 'integer',
        'balance'       => 'double',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * Returns a 40 character API key that a user can use
     * @return string
     */
    public static function generateApiKey()
    {
        $key = sha1(time() . mt_rand());
        return $key;
    }

    public function getPilotIdAttribute($value)
    {
        $length = setting('pilots.id_length');
        return $this->airline->icao . str_pad($this->id, $length, '0', STR_PAD_LEFT);
    }

    public function gravatar()
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

    public function flights()
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
