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
