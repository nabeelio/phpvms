<?php

namespace App\Models;

use App\Contracts\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property string name
 * @property string slug
 */
class PirepField extends Model
{
    public $table = 'pirep_fields';
    public $timestamps = false;

    protected $fillable = [
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
     * When setting the name attribute, also set the slug
     *
     * @return Attribute
     */
    public function name(): Attribute
    {
        return Attribute::make(
            set: fn ($name) => [
                'name' => $name,
                'slug' => str_slug($name),
            ]
        );
    }
}
