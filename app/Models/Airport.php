<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Traits\ExpensableTrait;
use App\Models\Traits\FilesTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Airport
 *
 * @property string id
 * @property string iata
 * @property string icao
 * @property string name
 * @property string full_name
 * @property string location
 * @property string country
 * @property string timezone
 * @property string notes
 * @property float  ground_handling_cost
 * @property float  fuel_100ll_cost
 * @property float  fuel_jeta_cost
 * @property float  fuel_mogas_cost
 * @property float  lat
 * @property float  lon
 */
class Airport extends Model
{
    use ExpensableTrait;
    use FilesTrait;
    use HasFactory;

    public $table = 'airports';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'iata',
        'icao',
        'name',
        'location',
        'country',
        'lat',
        'lon',
        'hub',
        'timezone',
        'tz',
        'ground_handling_cost',
        'fuel_100ll_cost',
        'fuel_jeta_cost',
        'fuel_mogas_cost',
        'notes',
    ];

    protected $casts = [
        'lat'                  => 'float',
        'lon'                  => 'float',
        'hub'                  => 'boolean',
        'ground_handling_cost' => 'float',
        'fuel_100ll_cost'      => 'float',
        'fuel_jeta_cost'       => 'float',
        'fuel_mogas_cost'      => 'float',
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        'icao'                 => 'required',
        'iata'                 => 'sometimes|nullable',
        'name'                 => 'required',
        'location'             => 'sometimes',
        'lat'                  => 'required|numeric',
        'lon'                  => 'required|numeric',
        'ground_handling_cost' => 'nullable|numeric',
        'fuel_100ll_cost'      => 'nullable|numeric',
        'fuel_jeta_cost'       => 'nullable|numeric',
        'fuel_mogas_cost'      => 'nullable|numeric',
    ];

    /**
     * Capitalize the ICAO
     */
    public function icao(): Attribute
    {
        return Attribute::make(
            set: fn ($icao) => [
                'id'   => strtoupper($icao),
                'icao' => strtoupper($icao),
            ]
        );
    }

    /**
     * Capitalize the IATA code
     */
    public function iata(): Attribute
    {
        return Attribute::make(
            set: fn ($iata) => strtoupper($iata)
        );
    }

    /**
     * Return full name like:
     * KJFK - John F Kennedy
     *
     * @return string
     */
    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn ($_, $attrs) => $this->icao.' - '.$this->name
        );
    }

    /**
     * Shortcut for timezone
     *
     * @return Attribute
     */
    public function tz(): Attribute
    {
        return Attribute::make(
            get: fn ($_, $attrs) => $attrs['timezone'],
            set: fn ($value) => [
                'timezone' => $value,
            ]
        );
    }
}
