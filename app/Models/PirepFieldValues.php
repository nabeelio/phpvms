<?php

namespace App\Models;

/**
 * Class PirepField
 *
 * @package App\Models
 */
class PirepFieldValues extends BaseModel
{
    public $table = 'pirep_field_values';

    public $fillable = [
        'pirep_id',
        'name',
        'value',
        'source',
    ];

    public static $rules = [
        'name' => 'required',
    ];

    /**
     * Foreign Keys
     */

    public function pirep()
    {
        return $this->belongsTo(Pirep::class, 'pirep_id');
    }
}
