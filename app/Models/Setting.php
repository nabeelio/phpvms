<?php

namespace App\Models;

/**
 * Class Setting
 * @property string id
 * @property string name
 * @property string key
 * @property string value
 * @property string group
 * @property string type
 * @property string options
 * @property string description
 * @package App\Models
 */
class Setting extends BaseModel
{
    public $table = 'settings';
    public $incrementing = false;

    public $fillable = [
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
     * @return mixed
     */
    public static function formatKey($key)
    {
        return str_replace('.', '_', strtolower($key));
    }

    /**
     * Force formatting the key
     * @param $id
     */
    public function setIdAttribute($id): void
    {
        $id = strtolower($id);
        $this->attributes['id'] = self::formatKey($id);
    }

    /**
     * Set the key to lowercase
     * @param $key
     */
    public function setKeyAttribute($key): void
    {
        $this->attributes['key'] = strtolower($key);
    }
}
