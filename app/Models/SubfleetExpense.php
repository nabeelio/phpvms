<?php

namespace App\Models;

/**
 * Class SubfleetExpense
 * @package App\Models
 */
class SubfleetExpense extends BaseModel
{
    public $table = 'subfleet_expenses';

    public $fillable = [
        'subfleet_id',
        'name',
        'amount',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amount'    => 'float',
    ];

    public static $rules = [
        'name'      => 'required',
        'amount'    => 'required|numeric',
    ];

    /**
     * Relationships
     */

    /**
     * Has a subfleet
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subfleet()
    {
        return $this->belongsTo(Subfleet::class, 'subfleet_id');
    }
}
