<?php

namespace App\Models;

/**
 * Class News
 * @package App\Models
 */
class News extends BaseModel
{
    public $table = 'news';

    public $fillable = [
        'user_id',
        'subject',
        'body',
    ];

    public static $rules = [
        'subject' => 'required',
        'body' => 'required',
    ];

    /**
     * FOREIGN KEYS
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
