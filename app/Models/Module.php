<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * Class ModuleManager
 */
class Module extends Model
{
    public $table = 'modules';

    public $fillable = [
        'name',
        'enabled',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static $rules = [
        'name' => 'required',
    ];
}
