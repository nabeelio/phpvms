<?php

namespace App\Models;

use App\Interfaces\Model;

/**
 * Class Rank
 *
 * @property int   hours
 * @property float manual_base_pay_rate
 * @property float acars_base_pay_rate
 */
class Rank extends Model
{
    public $table = 'ranks';

    protected $fillable = [
        'name',
        'hours',
        'image_url',
        'acars_base_pay_rate',
        'manual_base_pay_rate',
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
        'name'                 => 'required',
        'hours'                => 'required|integer',
        'acars_base_pay_rate'  => 'nullable|numeric',
        'manual_base_pay_rate' => 'nullable|numeric',
    ];

    /*
     * Relationships
     */

    public function subfleets()
    {
        return $this->belongsToMany(Subfleet::class, 'subfleet_rank')
            ->withPivot('acars_pay', 'manual_pay');
    }
}
