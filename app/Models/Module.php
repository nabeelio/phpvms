<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * Class ModuleManager
 * @package Modules\ModulesManager\Models
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
        'enabled' => 'boolean'
    ];

    public static $rules = [
        'module_name' => 'required'
    ];
}
