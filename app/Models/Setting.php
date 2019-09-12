<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * @property string id
 * @property string name
 * @property string key
 * @property string value
 * @property string group
 * @property string type
 * @property string options
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
     * @return mixed
     */
    public static function formatKey($key)
    {
        return str_replace('.', '_', strtolower($key));
    }

    /**
     * Force formatting the key
     *
     * @param $id
     */
    public function setIdAttribute($id): void
    {
        $id = strtolower($id);
        $this->attributes['id'] = self::formatKey($id);
    }

    /**
     * Set the key to lowercase
     *
     * @param $key
     */
    public function setKeyAttribute($key): void
    {
        $this->attributes['key'] = strtolower($key);
    }
}
