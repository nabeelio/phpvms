<?php

namespace App\Models;

use App\Contracts\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property string id
 * @property string name
 * @property string key
 * @property string value
 * @property string group
 * @property string type
 * @property string options
 * @property int    order
 * @property string description
 */
class Setting extends Model
{
    public $table = 'settings';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'key',
        'value',
        'group',
        'type',
        'options',
        'description',
    ];

    public static $rules = [
        'name'  => 'required',
        'key'   => 'required',
        'group' => 'required',
    ];

    /**
     * @param $key
     *
     * @return string
     */
    public static function formatKey($key): string
    {
        return str_replace('.', '_', strtolower($key));
    }

    /**
     * Force formatting the key
     *
     * @return Attribute
     */
    public function id(): Attribute
    {
        return Attribute::make(
            get: fn ($id, $attrs) => self::formatKey(strtolower($id))
        );
    }

    /**
     * Set the key to lowercase
     *
     * @return Attribute
     */
    public function key(): Attribute
    {
        return Attribute::make(
            set: fn ($key) => strtolower($key)
        );
    }
}
