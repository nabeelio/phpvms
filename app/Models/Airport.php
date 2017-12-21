<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class Airport
 * @package App\Models
 */
class Airport extends Model
{
    public $table = 'airports';
    public $timestamps = false;
    public $incrementing = false;

    public $fillable = [
        'id',
        'icao',
        'name',
        'location',
        'lat',
        'lon',
        'timezone',
        'fuel_100ll_cost',
        'fuel_jeta_cost',
        'fuel_mogas_cost',
    ];

    protected $casts = [
        'lat' => 'float',
        'lon' => 'float',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        #'icao' => 'required|unique:airports'
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
