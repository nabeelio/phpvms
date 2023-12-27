<?php

namespace App\Models;

use App\Contracts\Model;

class Invite extends Model
{
    public $table = 'invites';

    protected $fillable = [
        'email',
        'token',
        'usage_count',
        'usage_limit',
        'expires_at'
    ];

    protected $casts = [
        'email'       => 'string',
        'token'       => 'string',
        'usage_count' => 'integer',
        'usage_limit' => 'integer',
        'expires_at'  => 'datetime'
    ];

    public static array $rules = [
        'email'       => 'nullable|string',
        'token'       => 'required|string',
        'usage_count' => 'integer',
        'usage_limit' => 'nullable|integer',
        'expires_at'  => 'nullable|datetime'
    ];
}
