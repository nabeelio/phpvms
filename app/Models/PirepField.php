<?php

namespace App\Models;

/**
 * Class PirepField
 *
 * @package App\Models
 */
class PirepField extends BaseModel
{
    public $table = 'pirep_fields';
    public $timestamps = false;

    public $fillable = [
        'name',
        'slug',
        'required',
    ];

    protected $casts = [
        'required' => 'boolean',
    ];

    public static $rules = [
        'name' => 'required',
    ];

    /**
     * Create/update the field slug
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * On creation
         */
        static::creating(function (PirepField $model) {
            if (!empty($model->slug)) {
                $model->slug = str_slug($model->name);
            }
        });

        /**
         * When updating
         */
        static::updating(function(PirepField $model) {
            if (!empty($model->slug)) {
                $model->slug = str_slug($model->name);
            }
        });
    }
}
