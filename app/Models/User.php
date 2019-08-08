<?php

namespace App\Models;

use App\Models\Enums\JournalType;
use App\Models\Enums\PirepState;
use App\Models\Traits\JournalTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;

/**
 * @property int            id
 * @property int            pilot_id
 * @property string         name
 * @property string         email
 * @property string         password
 * @property string         api_key
 * @property mixed          timezone
 * @property string         ident
 * @property string         curr_airport_id
 * @property string         home_airport_id
 * @property Airline        airline
 * @property Flight[]       flights
 * @property int            flight_time
 * @property int            transfer_time
 * @property string         remember_token
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 * @property Rank           rank
 * @property Journal        journal
 * @property int            rank_id
 * @property int            state
 * @property bool           opt_in
 * @mixin \Illuminate\Notifications\Notifiable
 * @mixin \Laratrust\Traits\LaratrustUserTrait
 */
class User extends Authenticatable
{
    use JournalTrait;
    use LaratrustUserTrait;
    use Notifiable;

    public $table = 'users';

    /**
     * The journal type for when it's being created
     */
    public $journal_type = JournalType::USER;

    protected $fillable = [
        'id',
        'name',
        'email',
        'password',
        'pilot_id',
        'airline_id',
        'rank_id',
        'api_key',
        'country',
        'home_airport_id',
        'curr_airport_id',
        'last_pirep_id',
        'flights',
        'flight_time',
        'transfer_time',
        'avatar',
        'timezone',
        'state',
        'status',
        'toc_accepted',
        'opt_in',
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
        'id'            => 'integer',
        'pilot_id'      => 'integer',
        'flights'       => 'integer',
        'flight_time'   => 'integer',
        'transfer_time' => 'integer',
        'balance'       => 'double',
        'state'         => 'integer',
        'status'        => 'integer',
        'toc_accepted'  => 'boolean',
        'opt_in'        => 'boolean',
    ];

    public static $rules = [
        'name'     => 'required',
        'email'    => 'required|email',
        'pilot_id' => 'required|integer',
    ];

    /**
     * @return string
     */
    public function getIdentAttribute()
    {
        $length = setting('pilots.id_length');

        return $this->airline->icao.str_pad($this->pilot_id, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Shorthand for getting the timezone
     *
     * @return string
     */
    public function getTzAttribute(): string
    {
        return $this->timezone;
    }

    /**
     * Shorthand for setting the timezone
     *
     * @param $value
     */
    public function setTzAttribute($value)
    {
        $this->attributes['timezone'] = $value;
    }

    /**
     * Return a File model
     */
    public function getAvatarAttribute()
    {
        if (!$this->attributes['avatar']) {
            return;
        }

        return new File([
           'path' => $this->attributes['avatar'],
        ]);
    }

    /**
     * @param mixed $size Size of the gravatar, in pixels
     *
     * @return string
     */
    public function gravatar($size = null)
    {
        $default = config('gravatar.default');

        $uri = config('gravatar.url')
            .md5(strtolower(trim($this->email))).'?d='.urlencode($default);

        if ($size !== null) {
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

    public function awards()
    {
        return $this->hasMany(UserAward::class, 'user_id');
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

    /**
     * These are the flights they've bid on
     */
    public function flights()
    {
        return $this->belongsToMany(Flight::class, 'bids');
    }

    /**
     * The bid rows
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bids()
    {
        return $this->hasMany(Bid::class, 'user_id');
    }

    public function pireps()
    {
        return $this->hasMany(Pirep::class, 'user_id')
            ->where('state', '!=', PirepState::CANCELLED);
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class, 'rank_id');
    }
}
