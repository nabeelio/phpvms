<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Traits\ExpensableTrait;
use App\Models\Traits\FilesTrait;

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
     * @param $icao
     */
    public function setIcaoAttribute($icao)
    {
        $icao = strtoupper($icao);
        $this->attributes['id'] = $icao;
        $this->attributes['icao'] = $icao;
    }

    /**
     * @param $iata
     */
    public function setIataAttribute($iata): void
    {
        $iata = strtoupper($iata);
        $this->attributes['iata'] = $iata;
    }

    /**
     * Return full name like:
     * KJFK - John F Kennedy
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->icao.' - '.$this->name;
    }

    /**
     * Shorthand for getting the timezone
     *
     * @return string
     */
    public function getTzAttribute(): string
    {
        return $this->attributes['timezone'];
    }

    /**
     * Shorthand for setting the timezone
     *
     * @param $value
     */
    public function setTzAttribute($value): void
    {
        $this->attributes['timezone'] = $value;
    }
}
