<?php

namespace App\Models;

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
     * Callbacks
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function (Setting $model) {
            if (!empty($model->id)) {
                $model->id = Setting::formatKey($model->id);
            }
        });
    }
}
