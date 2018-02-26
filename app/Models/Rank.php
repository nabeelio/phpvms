<?php

namespace App\Models;

/**
 * Class Rank
 * @package App\Models
 */
class Rank extends BaseModel
{
    public $table = 'ranks';

    public $fillable = [
        'name',
        'hours',
        'image_link',
        'base_pay_rate',
        'auto_approve_acars',
        'auto_approve_manual',
        'auto_promote',
    ];

    protected $casts = [
        'hours'               => 'integer',
        'base_pay_rate'       => 'float',
        'auto_approve_acars'  => 'bool',
        'auto_approve_manual' => 'bool',
        'auto_promote'        => 'bool',
    ];

    public static $rules = [
        'name'  => 'required',
        'hours' => 'required|integer',
        'base_pay_rate' => 'nullable|numeric',
    ];

    public function subfleets() {
        return $this->belongsToMany(Subfleet::class, 'subfleet_rank')
                    ->withPivot('acars_pay', 'manual_pay');
    }
}
