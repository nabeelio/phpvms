<?php

namespace App\Models;

use Eloquent as Model;

/**
 * Class PirepEvent
 *
 * @package App\Models
 */
class PirepComment extends Model
{
    public $table = 'pirep_comments';

    public $fillable
        = [
            'comment',
        ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules
        = [
            'comment' => 'required',
        ];

    public function pirep()
    {
        return $this->belongsTo('App\Models\Pirep', 'pirep_id');
    }
}
