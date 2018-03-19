<?php

namespace App\Models;

/**
 * Class PirepField
 * @property string name
 * @property string slug
 * @package App\Models
 */
class PirepField extends BaseModel
{
    public $table = 'pirep_fields';
    public $timestamps = false;

    public $fillable = [
        'name',
        'slug',
        'required',
    ];

    protected $casts = [
        'required' => 'boolean',
    ];

    public static $rules = [
        'name' => 'required',
    ];

    /**
     * When setting the name attribute, also set the slug
     * @param $name
     */
    public function setNameAttribute($name): void
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = str_slug($name);
    }
}
