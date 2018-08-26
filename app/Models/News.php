<?php

namespace App\Models;

use App\Interfaces\Model;

/**
 * Class News
 */
class News extends Model
{
    public $table = 'news';

    protected $fillable = [
        'user_id',
        'subject',
        'body',
    ];

    public static $rules = [
        'subject' => 'required',
        'body'    => 'required',
    ];

    /**
     * FOREIGN KEYS
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
