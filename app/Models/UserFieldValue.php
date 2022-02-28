<?php

namespace App\Models;

use App\Contracts\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property string    name
 * @property string    value
 * @property UserField field
 * @property User      user
 */
class UserFieldValue extends Model
{
    public $table = 'user_field_values';

    protected $fillable = [
        'user_field_id',
        'user_id',
        'value',
    ];

    public static $rules = [];

    /**
     * Return related field's name along with field values
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($_, $attrs) => optional($this->field)->name
        );
    }

    /**
     * Foreign Keys
     */
    public function field()
    {
        return $this->belongsTo(UserField::class, 'user_field_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
