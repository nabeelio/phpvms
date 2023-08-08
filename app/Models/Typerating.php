<?php

namespace App\Models;

use App\Contracts\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Typerating extends Model
{
    public $table = 'typeratings';

    protected $fillable = [
        'name',
        'type',
        'description',
        'image_url',
        'active',
    ];

    // Validation
    public static $rules = [
        'name'        => 'required',
        'type'        => 'required',
        'description' => 'nullable',
        'image_url'   => 'nullable',
    ];

    // Relationships
    public function subfleets(): BelongsToMany
    {
        return $this->belongsToMany(Subfleet::class, 'typerating_subfleet', 'typerating_id', 'subfleet_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'typerating_user', 'typerating_id', 'user_id');
    }
}
