<?php

namespace App\Models;

/**
 * Class Airline
 * @package App\Models
 */
class Airline extends BaseModel
{
    public $table = 'airlines';

    public $fillable = [
        'icao',
        'iata',
        'name',
        'logo',
        'country',
        'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'code' => 'required|max:3|unique:airlines',
        'name' => 'required',
    ];

    /**
     * For backwards compatibility
     */
    public function getCodeAttribute() {
        return $this->icao;
    }

}
