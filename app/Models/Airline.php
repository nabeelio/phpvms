<?php

namespace App\Models;

use App\Models\Traits\JournalTrait;

/**
 * Class Airline
 * @package App\Models
 */
class Airline extends BaseModel
{
    use JournalTrait;

    public $table = 'airlines';

    public $fillable = [
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
        'country'   => 'nullable',
        'iata'      => 'nullable|max:5',
        'icao'      => 'required|max:5',
        'logo'      => 'nullable',
        'name'      => 'required',
    ];

    /**
     * For backwards compatibility
     */
    public function getCodeAttribute() {
        return $this->icao;
    }

    protected static function boot()
    {
        parent::boot();

        /**
         * IATA and ICAO should be in all caps
         */
        static::creating(function (Airline $model) {
            if (!empty($model->iata)) {
                $model->iata = strtoupper($model->iata);
            }

            if (!empty($model->icao)) {
                $model->icao = strtoupper($model->icao);
            }
        });
    }
}
