<?php

namespace App\Models;

use App\Contracts\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property string name
 * @property string slug
 * @property string value  Only set if "squashed"
 * @property bool   show_on_registration
 * @property bool   required
 * @property bool   private
 */
class UserField extends Model
{
    public $table = 'user_fields';

    protected $fillable = [
        'name',
        'description',
        'show_on_registration', // Show on the registration form?
        'required',             // Required to be filled out in registration?
        'private',              // Whether this is shown on the user's public profile
        'active',
    ];

    protected $casts = [
        'show_on_registration' => 'boolean',
        'required'             => 'boolean',
        'private'              => 'boolean',
        'active'               => 'boolean',
    ];

    public static $rules = [
        'name'        => 'required',
        'description' => 'nullable',
    ];

    /**
     * Get the slug so we can use it in forms
     *
     * @return Attribute
     */
    public function slug(): Attribute
    {
        return Attribute::make(
            get: fn ($_, $attrs) => str_slug($attrs['name'], '_')
        );
    }
}
