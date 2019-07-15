<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * Class UserAward
 */
class UserAward extends Model
{
    public $table = 'user_awards';

    protected $fillable = [
        'user_id',
        'award_id',
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
