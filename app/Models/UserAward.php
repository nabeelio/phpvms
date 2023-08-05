<?php

namespace App\Models;

use App\Contracts\Model;
use App\Events\AwardAwarded;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class UserAward extends Model
{
    use Notifiable;
    public $table = 'user_awards';

    protected $fillable = [
        'user_id',
        'award_id',
    ];

    protected $dispatchesEvents = [
        'created' => AwardAwarded::class,
    ];

    /**
     * Relationships
     */
    public function award(): BelongsTo
    {
        return $this->belongsTo(Award::class, 'award_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
