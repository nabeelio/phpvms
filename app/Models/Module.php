<?php

namespace App\Models;

use App\Contracts\Model;
use Carbon\Carbon;

/**
 * @property string name
 * @property bool   enabled
 * @property Carbon created_at
 * @property Carbon updated_at
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
