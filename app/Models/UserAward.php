<?php

namespace App\Models;

use App\Interfaces\Model;

/**
 * Class UserAward
 * @package App\Models
 */
class UserAward extends Model
{
    public $table = 'user_awards';

    protected $fillable = [
        'user_id',
        'award_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function award()
    {
        return $this->belongsTo(Award::class, 'award_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
