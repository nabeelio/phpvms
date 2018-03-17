<?php

namespace App\Models;

/**
 * Class Award
 *
 * @package Award\Models
 */
class Award extends BaseModel
{
    public $table = 'awards';

    public $fillable = [
        'title',
        'description',
        'image',
    ];

    protected $casts = [

    ];

    public static $rules = [
        'title' => 'required',
    ];


    /**
     * any foreign keys
     */
    /*
        public function subfleets() {
            return $this->belongsToMany(Subfleet::class, 'subfleet_fare')
                ->withPivot('price', 'cost', 'capacity');
        }
        */
}
