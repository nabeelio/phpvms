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
}
