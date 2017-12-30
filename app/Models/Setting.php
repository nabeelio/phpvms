<?php
/**
 * Created by IntelliJ IDEA.
 * User: nshahzad
 * Date: 12/9/17
 * Time: 6:24 PM
 */

namespace App\Models;

class Setting extends BaseModel
{
    public $table = 'settings';

    public $fillable = [
        'name',
        'key',
        'value',
        'group',
        'type',
        'options',
        'description',
    ];

}
