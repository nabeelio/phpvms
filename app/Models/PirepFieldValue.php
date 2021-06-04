<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Enums\PirepFieldSource;

/**
 * @property string pirep_id
 * @property string name
 * @property string slug
 * @property string value
 * @property string source
 * @property Pirep  pirep
 *
 * @method static updateOrCreate(array $array, array $array1)
 */
class PirepFieldValue extends Model
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

    protected $casts = [
        'source' => 'integer',
    ];

    /**
     * If it was filled in from ACARS, then it's read only
     *
     * @return bool
     */
    public function getReadOnlyAttribute()
    {
        return $this->source === PirepFieldSource::ACARS;
    }

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
