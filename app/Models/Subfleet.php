<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subfleet
 * @package App\Models
 */
class Subfleet extends Model
{
    use SoftDeletes;

    public $table = 'subfleets';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'airline_id',
        'name',
        'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'airline_id' => 'integer',
        'name' => 'string',
        'type' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function airline()
    {
        return $this->belongsTo('App\Models\Airline', 'airline_id');
    }

    public function ranks()
    {
        return $this->belongsToMany(
            'App\Models\Ranks',
            'subfleet_rank'
        )->withPivot('acars_pay', 'manual_pay');
    }
}
