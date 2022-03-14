<?php

namespace App\Models;

use App\Contracts\Model;
use Carbon\Carbon;

/**
 * @property string $pirep_id
 * @property string $comment
 * @property int    $user_id
 * @property Pirep  $pirep
 * @property User   $user
 * @property Carbon $created_at
 */
class PirepComment extends Model
{
    public $table = 'pirep_comments';

    protected $fillable = [
        'pirep_id',
        'user_id',
        'comment',
    ];

    public static $rules = [
        'comment' => 'required',
    ];

    public function pirep()
    {
        return $this->belongsTo(Pirep::class, 'pirep_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
