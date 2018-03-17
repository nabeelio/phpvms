<?php

namespace App\Models;

/**
 * Class UserAward
 * @package App\Models
 */
class UserAward extends BaseModel
{
    public $table = 'user_awards';

    public $fillable = [
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
