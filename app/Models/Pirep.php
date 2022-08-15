<?php

namespace App\Models;

use App\Contracts\Model;
use App\Events\PirepStateChange;
use App\Events\PirepStatusChange;
use App\Models\Casts\CarbonCast;
use App\Models\Casts\DistanceCast;
use App\Models\Casts\FuelCast;
use App\Models\Enums\AcarsType;
use App\Models\Enums\PirepFieldSource;
use App\Models\Enums\PirepState;
use App\Models\Traits\HashIdTrait;
use App\Support\Units\Distance;
use App\Support\Units\Fuel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Kleemans\AttributeEvents;

/**
 * @property string      id
 * @property string      ident
 * @property string      flight_number
 * @property string      route_code
 * @property string      route_leg
 * @property string      flight_type
 * @property int         airline_id
 * @property int         user_id
 * @property int         aircraft_id
 * @property Aircraft    aircraft
 * @property Airline     airline
 * @property Airport     arr_airport
 * @property string      arr_airport_id
 * @property Airport     dpt_airport
 * @property string      dpt_airport_id
 * @property Carbon      block_off_time
 * @property Carbon      block_on_time
 * @property int         block_time
 * @property int         flight_time    In minutes
 * @property int         planned_flight_time
 * @property Fuel        block_fuel
 * @property Fuel        fuel_used
 * @property Distance    distance
 * @property Distance    planned_distance
 * @property float       progress_percent
 * @property int         level
 * @property string      route
 * @property int         score
 * @property User        user
 * @property Flight|null flight
 * @property Collection  fields
 * @property string      status
 * @property int         state
 * @property int         source
 * @property string      source_name
 * @property Carbon      submitted_at
 * @property Carbon      created_at
 * @property Carbon      updated_at
 * @property bool        read_only
 * @property Acars       position
 * @property Acars[]     acars
 * @property mixed       cancelled
 */
class Pirep extends Model
{
    use AttributeEvents;
    use HashIdTrait;
    use HasFactory;
    use Notifiable;

    public $table = 'pireps';
    protected $keyType = 'string';
    public $incrementing = false;
    /** The form wants this */
    public $hours;
    public $minutes;
    protected $fillable = [
        'id',
        'user_id',
        'airline_id',
        'aircraft_id',
        'flight_number',
        'route_code',
        'route_leg',
        'flight_id',
        'dpt_airport_id',
        'arr_airport_id',
        'alt_airport_id',
        'level',
        'distance',
        'planned_distance',
        'block_time',
        'flight_time',
        'planned_flight_time',
        'zfw',
        'block_fuel',
        'fuel_used',
        'landing_rate',
        'route',
        'notes',
        'score',
        'source',
        'source_name',
        'flight_type',
        'state',
        'status',
        'block_off_time',
        'block_on_time',
        'submitted_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'user_id'             => 'integer',
        'airline_id'          => 'integer',
        'aircraft_id'         => 'integer',
        'level'               => 'integer',
        'distance'            => DistanceCast::class,
        'planned_distance'    => DistanceCast::class,
        'block_time'          => 'integer',
        'block_off_time'      => CarbonCast::class,
        'block_on_time'       => CarbonCast::class,
        'flight_time'         => 'integer',
        'planned_flight_time' => 'integer',
        'zfw'                 => 'float',
        'block_fuel'          => FuelCast::class,
        'fuel_used'           => FuelCast::class,
        'landing_rate'        => 'float',
        'score'               => 'integer',
        'source'              => 'integer',
        'state'               => 'integer',
        'submitted_at'        => CarbonCast::class,
    ];
    public static $rules = [
        'airline_id'     => 'required|exists:airlines,id',
        'aircraft_id'    => 'required|exists:aircraft,id',
        'flight_number'  => 'required',
        'dpt_airport_id' => 'required',
        'arr_airport_id' => 'required',
        'block_fuel'     => 'nullable|numeric',
        'fuel_used'      => 'nullable|numeric',
        'level'          => 'nullable|numeric',
        'notes'          => 'nullable',
        'route'          => 'nullable',
    ];
    /**
     * Auto-dispatch events for lifecycle state changes
     */
    protected $dispatchesEvents = [
        'status:*' => PirepStatusChange::class,
        'state:*'  => PirepStateChange::class,
    ];
    /*
     * If a PIREP is in these states, then it can't be changed.
     */
    public static $read_only_states = [
        PirepState::ACCEPTED,
        PirepState::REJECTED,
        PirepState::CANCELLED,
    ];
    /*
     * If a PIREP is in one of these states, it can't be cancelled
     */
    public static $cancel_states = [
        PirepState::ACCEPTED,
        PirepState::REJECTED,
        PirepState::CANCELLED,
        PirepState::DELETED,
    ];

    /**
     * Create a new PIREP model from a given flight. Pre-populates the fields
     *
     * @param \App\Models\Flight $flight
     *
     * @return \App\Models\Pirep
     */
    public static function fromFlight(Flight $flight): self
    {
        return new self([
            'flight_id'      => $flight->id,
            'airline_id'     => $flight->airline_id,
            'flight_number'  => $flight->flight_number,
            'route_code'     => $flight->route_code,
            'route_leg'      => $flight->route_leg,
            'dpt_airport_id' => $flight->dpt_airport_id,
            'arr_airport_id' => $flight->arr_airport_id,
            'route'          => $flight->route,
            'level'          => $flight->level,
        ]);
    }

    /**
     * Create a new PIREP from a SimBrief instance
     *
     * @param \App\Models\SimBrief $simbrief
     *
     * @return \App\Models\Pirep
     */
    public static function fromSimBrief(SimBrief $simbrief): self
    {
        return new self([
            'flight_id'      => $simbrief->flight->id,
            'airline_id'     => $simbrief->flight->airline_id,
            'flight_number'  => $simbrief->flight->flight_number,
            'route_code'     => $simbrief->flight->route_code,
            'route_leg'      => $simbrief->flight->route_leg,
            'dpt_airport_id' => $simbrief->flight->dpt_airport_id,
            'arr_airport_id' => $simbrief->flight->arr_airport_id,
            'route'          => $simbrief->xml->getRouteString(),
            'level'          => $simbrief->xml->getFlightLevel(),
        ]);
    }

    /**
     * Get the flight ident, e.,g JBU1900/C.nn/L.yy
     */
    public function ident(): Attribute
    {
        return Attribute::make(get: function ($value, $attrs) {
            $flight_id = optional($this->airline)->code;
            $flight_id .= $this->flight_number;

            if (filled($this->route_code)) {
                $flight_id .= '/C.'.$this->route_code;
            }

            if (filled($this->route_leg)) {
                $flight_id .= '/L.'.$this->route_leg;
            }

            return $flight_id;
        });
    }

    /**
     * Return if this PIREP can be edited or not
     */
    public function readOnly(): Attribute
    {
        return Attribute::make(get: fn ($_, $attrs) => \in_array($this->state, static::$read_only_states, true));
    }

    /**
     * Return the flight progress in a percent.
     */
    public function progressPercent(): Attribute
    {
        return Attribute::make(get: function ($_, $attrs) {
            $distance = $attrs['distance'];

            $upper_bound = $distance;
            if (!empty($attrs['planned_distance']) && $attrs['planned_distance'] > 0) {
                $upper_bound = $attrs['planned_distance'];
            }

            $upper_bound = empty($upper_bound) ? 1 : $upper_bound;
            $distance = empty($distance) ? $upper_bound : $distance;

            return round(($distance / $upper_bound) * 100);
        });
    }

    /**
     * Get the pirep_fields and then the pirep_field_values and
     * merge them together. If a field value doesn't exist then add in a fake one
     */
    public function fields(): Attribute
    {
        return Attribute::make(get: function ($_, $attrs) {
            $custom_fields = PirepField::all();
            $field_values = PirepFieldValue::where('pirep_id', $this->id)->orderBy('created_at', 'asc')->get();

            // Merge the field values into $fields
            foreach ($custom_fields as $field) {
                $has_value = $field_values->firstWhere('slug', $field->slug);
                if (!$has_value) {
                    $field_values->push(new PirepFieldValue([
                        'pirep_id' => $this->id,
                        'name'     => $field->name,
                        'slug'     => $field->slug,
                        'value'    => '',
                        'source'   => PirepFieldSource::MANUAL,
                    ]));
                }
            }

            return $field_values;
        });
    }

    /**
     * Do some cleanup on the route
     *
     * @return Attribute
     */
    public function route(): Attribute
    {
        return Attribute::make(set: fn ($route) => strtoupper(trim($route)));
    }

    /**
     * Return if this is cancelled or not
     */
    public function cancelled(): Attribute
    {
        return Attribute::make(get: fn ($_, $attrs) => $this->state === PirepState::CANCELLED);
    }

    /**
     * Check if this PIREP is allowed to be updated
     *
     * @return bool
     */
    public function allowedUpdates(): bool
    {
        return !$this->getReadOnlyAttribute();
    }

    /**
     * Return a custom field value
     *
     * @param $field_name
     *
     * @return string
     */
    public function field($field_name): string
    {
        $field = $this->fields->where('name', $field_name)->first();
        if ($field) {
            return $field['value'];
        }

        return '';
    }

    /**
     * Foreign Keys
     */
    public function acars()
    {
        return $this->hasMany(Acars::class, 'pirep_id')->where('type', AcarsType::FLIGHT_PATH)->orderBy('created_at', 'asc')->orderBy('sim_time', 'asc');
    }

    public function acars_logs()
    {
        return $this->hasMany(Acars::class, 'pirep_id')->where('type', AcarsType::LOG)->orderBy('created_at', 'desc')->orderBy('sim_time', 'asc');
    }

    public function acars_route()
    {
        return $this->hasMany(Acars::class, 'pirep_id')->where('type', AcarsType::ROUTE)->orderBy('order', 'asc');
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id');
    }

    public function airline()
    {
        return $this->belongsTo(Airline::class, 'airline_id');
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class, 'flight_id');
    }

    public function arr_airport()
    {
        return $this->belongsTo(Airport::class, 'arr_airport_id')->withDefault(function ($model) {
            if (!empty($this->attributes['arr_airport_id'])) {
                $model->id = $this->attributes['arr_airport_id'];
                $model->icao = $this->attributes['arr_airport_id'];
                $model->name = $this->attributes['arr_airport_id'];
            }

            return $model;
        });
    }

    public function alt_airport()
    {
        return $this->belongsTo(Airport::class, 'alt_airport_id');
    }

    public function dpt_airport()
    {
        return $this->belongsTo(Airport::class, 'dpt_airport_id')->withDefault(function ($model) {
            if (!empty($this->attributes['dpt_airport_id'])) {
                $model->id = $this->attributes['dpt_airport_id'];
                $model->icao = $this->attributes['dpt_airport_id'];
                $model->name = $this->attributes['dpt_airport_id'];
            }

            return $model;
        });
    }

    public function comments()
    {
        return $this->hasMany(PirepComment::class, 'pirep_id')->orderBy('created_at', 'desc');
    }

    public function fares()
    {
        return $this->hasMany(PirepFare::class, 'pirep_id');
    }

    public function field_values()
    {
        return $this->hasMany(PirepFieldValue::class, 'pirep_id');
    }

    public function pilot()
    {
        return $this->user();
    }

    /**
     * Relationship that holds the current position, but limits the ACARS
     *  relationship to only one row (the latest), to prevent an N+! problem
     */
    public function position()
    {
        return $this->hasOne(Acars::class, 'pirep_id')->where('type', AcarsType::FLIGHT_PATH)->latest();
    }

    public function simbrief()
    {
        return $this->belongsTo(SimBrief::class, 'id', 'pirep_id');
    }

    public function transactions()
    {
        return $this->hasMany(JournalTransaction::class, 'ref_model_id')->where('ref_model', __CLASS__)->orderBy('credit', 'desc')->orderBy('debit', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
