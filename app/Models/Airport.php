<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;

/**
 * Class Airport
 * @package App\Models
 */
class Airport extends BaseModel
{
    use Notifiable;

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
     * Callbacks
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if(filled($model->iata)) {
                $model->iata = strtoupper(trim($model->iata));
            }

            $model->icao = strtoupper(trim($model->icao));
            $model->id = $model->icao;
        });

        static::updating(function($model) {
            if (filled($model->iata)) {
                $model->iata = strtoupper(trim($model->iata));
            }

            $model->icao = strtoupper(trim($model->icao));
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

    /**
     * Shorthand for getting the timezone
     * @return string
     */
    public function getTzAttribute(): string
    {
        return $this->timezone;
    }

    /**
     * Shorthand for setting the timezone
     * @param $value
     */
    public function setTzAttribute($value)
    {
        $this->attributes['timezone'] = $value;
    }
}
