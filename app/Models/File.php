<?php

namespace App\Models;


use App\Interfaces\Model;
use App\Models\Traits\ReferenceTrait;
use Illuminate\Support\Facades\Storage;

/**
 * File property
 * @property string  $name
 * @property string  $description
 * @property string  $path
 * @property boolean $public
 * @package App\Models
 */
class File extends Model
{
    use ReferenceTrait;

    protected $table = 'files';

    protected $fillable = [
        'name',
        'description',
        'path',
        'public',
        'ref_model',
        'ref_model_id',
    ];

    public static $rules = [
        'name'        => 'required',
    ];

    /**
     * Get the full URL to this attribute
     * @return string
     */
    public function getUrlAttribute(): string
    {
        $disk = config('filesystems.public_files');
        if ($disk !== 'public') {
            return Storage::disk(config('filesystems.public_files'))
                ->url($this->path);
        }

        return public_asset(Storage::disk('public')
            ->url($this->path)
        );
    }
}
