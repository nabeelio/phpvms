<?php

namespace App\Models;

use App\Interfaces\Model;

/**
 * Class PirepFieldValues
 * @package App\Models
 */
class PirepFieldValues extends Model
{
    public $table = 'pirep_field_values';

    protected $fillable = [
        'pirep_id',
        'name',
        'slug',
        'value',
        'source',
    ];

    public static $rules = [
        'name' => 'required',
    ];

    /**
     * @param $name
     */
    public function setNameAttribute($name): void
    {
        $this->attributes['name'] = $name;
        $this->attributes['slug'] = str_slug($name);
    }

    /**
     * Foreign Keys
     */

    public function pirep()
    {
        return $this->belongsTo(Pirep::class, 'pirep_id');
    }
}
