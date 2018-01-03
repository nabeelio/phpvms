<?php

namespace App\Models;

/**
 * Class Airport
 * @package App\Models
 */
class Airport extends BaseModel
{
    public $table = 'airports';
    public $timestamps = false;
    public $incrementing = false;

    public $fillable = [
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
        'fuel_100ll_cost',
        'fuel_jeta_cost',
        'fuel_mogas_cost',
    ];

    protected $casts = [
        'lat' => 'float',
        'lon' => 'float',
        'hub' => 'boolean',
        'fuel_100ll_cost' => 'float',
        'fuel_jeta_cost' => 'float',
        'fuel_mogas_cost' => 'float',
    ];

    /**
     * Validation rules
     */
    public static $rules = [
        'icao' =>   'required',
        'name' =>   'required',
        'lat' =>    'required',
        'lon' =>    'required',
    ];

    /**
     * Some fancy callbacks
     */
    protected static function boot()
    {

        parent::boot();

        /**
         * Make sure the ID is set to the ICAO
         */
        static::creating(function (Airport $model) {
            if(!empty($model->iata)) {
                $model->iata = strtoupper($model->iata);
            }

            $model->icao = strtoupper($model->icao);
            $model->id = $model->icao;
        });
    }

    /**
     * Return full name like:
     * KJFK - John F Kennedy
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->icao . ' - ' . $this->name;
    }
}
