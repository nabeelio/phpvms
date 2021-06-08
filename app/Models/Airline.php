<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Enums\JournalType;
use App\Models\Traits\FilesTrait;
use App\Models\Traits\JournalTrait;
use Illuminate\Support\Str;

/**
 * Class Airline
 *
 * @property mixed   id
 * @property string  code
 * @property string  icao
 * @property string  iata
 * @property string  name
 * @property string  logo
 * @property string  country
 * @property Journal journal
 */
class Airline extends Model
{
    use FilesTrait;
    use JournalTrait;

    public $table = 'airlines';

    /**
     * The journal type for the callback
     */
    public $journal_type = JournalType::AIRLINE;

    protected $fillable = [
        'icao',
        'iata',
        'name',
        'logo',
        'country',
        'total_flights',
        'total_time',
        'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'total_flights' => 'int',
        'total_time'    => 'int',
        'active'        => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'country' => 'nullable',
        'iata'    => 'nullable|max:5',
        'icao'    => 'required|max:5',
        'logo'    => 'nullable',
        'name'    => 'required',
    ];

    /**
     * For backwards compatibility
     */
    public function getCodeAttribute()
    {
        if ($this->iata && $this->iata !== '') {
            return $this->iata;
        }
        return $this->icao;
    }

    /**
     * Capitalize the IATA code when set
     *
     * @param $iata
     */
    public function setIataAttribute($iata)
    {
        $this->attributes['iata'] = Str::upper($iata);
    }

    /**
     * Capitalize the ICAO when set
     *
     * @param $icao
     */
    public function setIcaoAttribute($icao): void
    {
        $this->attributes['icao'] = Str::upper($icao);
    }

    public function subfleets()
    {
        return $this->hasMany(Subfleet::class, 'airline_id');
    }

    public function flights()
    {
        return $this->belongsTo(Flight::class, 'airline_id');
    }

    public function pireps()
    {
        return $this->belongsTo(Pirep::class, 'airline_id');
    }
}
