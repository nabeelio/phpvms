<?php

namespace App\Models;

use App\Contracts\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * @property int       id
 * @property int|mixed user_id
 * @property string    subject
 * @property string    body
 * @property User      user
 */
class News extends Model
{
    use HasFactory;
    use Notifiable;

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
