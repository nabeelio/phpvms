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

    protected static function boot()
    {
        parent::boot();

        /**
         * Make sure any dots are replaced with underscores
         */
        static::creating(function (Setting $model) {
            if (!empty($model->id)) {
                $model->id = str_replace('.', '_', strtolower($model->id));
            }
        });
    }

}
