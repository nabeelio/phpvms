<?php

namespace App\Models;

use App\Contracts\Model;

/**
 * @property int    id
 * @property string name
 * @property string slug
 * @property string icon
 * @property int    type
 * @property bool   public
 * @property bool   enabled
 * @property string body
 */
class Page extends Model
{
    public $table = 'pages';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'icon',
        'public',
        'body',
        'enabled',
    ];

    protected $casts = [
        'type'    => 'integer',
        'public'  => 'boolean',
        'enabled' => 'boolean',
    ];

    public static $rules = [
        'name' => 'required|unique:pages,name',
        'body' => 'required',
    ];
}
