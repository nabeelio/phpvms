<?php

namespace App\Models;

/**
 * Class Ranking
 * @package App\Models
 */
class Rank extends BaseModel
{

    public $table = 'ranks';

    public $fillable = [
        'name',
        'hours',
        'image_link',
        'auto_approve_acars',
        'auto_approve_manual',
        'auto_promote',
    ];

    protected $casts = [
        'hours'               => 'integer',
        'auto_approve_acars'  => 'bool',
        'auto_approve_manual' => 'bool',
        'auto_promote'        => 'bool',
    ];

    public static $rules = [
        'name'  => 'required',
        'hours' => 'required|integer',
    ];

    public function subfleets() {
        return $this->belongsToMany('App\Models\Subfleet', 'subfleet_rank')
                    ->withPivot('acars_pay', 'manual_pay');
    }
}
