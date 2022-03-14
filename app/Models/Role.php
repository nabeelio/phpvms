<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laratrust\Models\LaratrustRole;

/**
 * @property int id
 * @property string name
 * @property string display_name
 * @property bool read_only
 * @property bool disable_activity_checks
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Role extends LaratrustRole
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'display_name',
        'read_only',
        'disable_activity_checks',
    ];

    protected $casts = [
        'read_only'               => 'boolean',
        'disable_activity_checks' => 'boolean',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'display_name' => 'required',
    ];
}
