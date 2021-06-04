<?php

namespace App\Models;

use App\Models\Enums\JournalType;
use App\Models\Traits\JournalTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;

/**
 * @property int              id
 * @property int              pilot_id
 * @property int              airline_id
 * @property string           name
 * @property string           name_private Only first name, rest are initials
 * @property string           email
 * @property string           password
 * @property string           api_key
 * @property mixed            timezone
 * @property string           ident
 * @property string           curr_airport_id
 * @property string           home_airport_id
 * @property string           avatar
 * @property Airline          airline
 * @property Flight[]         flights
 * @property int              flight_time
 * @property int              transfer_time
 * @property string           remember_token
 * @property \Carbon\Carbon   created_at
 * @property \Carbon\Carbon   updated_at
 * @property Rank             rank
 * @property Journal          journal
 * @property int              rank_id
 * @property string           discord_id
 * @property int              state
 * @property string           last_ip
 * @property bool             opt_in
 * @property Pirep[]          pireps
 * @property string           last_pirep_id
 * @property Pirep            last_pirep
 * @property UserFieldValue[] fields
 * @property Role[]           roles
 * @property Subfleet[]       subfleets
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
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
        'discord_id',
        'discord_private_channel_id',
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
        'last_ip',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'api_key',
        'discord_id',
        'password',
        'last_ip',
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
    public function getIdentAttribute(): string
    {
        $length = setting('pilots.id_length');

        return $this->airline->icao.str_pad($this->pilot_id, $length, '0', STR_PAD_LEFT);
    }

    /**
     * Return a "privatized" version of someones name - First name full, rest of the names are initials
     *
     * @return string
     */
    public function getNamePrivateAttribute(): string
    {
        $name_parts = explode(' ', $this->attributes['name']);
        $count = count($name_parts);
        if ($count === 1) {
            return $name_parts[0];
        }

        $first_name = $name_parts[0];
        $last_name = $name_parts[$count - 1];

        return $first_name.' '.$last_name[0];
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
            return null;
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

    public function resolveAvatarUrl()
    {
        $avatar = $this->getAvatarAttribute();
        if (empty($avatar)) {
            return $this->gravatar();
        }
        return $avatar->url;
    }

    /**
     * Foreign Keys
     */
    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    /**
     * @return \App\Models\Award[]|mixed
     */
    public function awards()
    {
        return $this->belongsToMany(Award::class, 'user_awards');
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

    public function fields()
    {
        return $this->hasMany(UserFieldValue::class, 'user_id');
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
