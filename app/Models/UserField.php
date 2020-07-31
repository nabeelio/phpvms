<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * @property string name
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
        'show_on_registration',
        'required',
        'private',
    ];

    protected $casts = [
        'show_on_registration' => 'boolean',
        'required'             => 'boolean',
        'private'              => 'boolean',
    ];

    public static $rules = [
        'name'        => 'required',
        'description' => 'nullable',
    ];
}
