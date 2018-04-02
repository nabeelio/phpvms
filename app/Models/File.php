<?php

namespace App\Models;


use App\Interfaces\Model;
use App\Models\Traits\ReferenceTrait;
use Illuminate\Support\Facades\Storage;

/**
 * File property
 * @property string  $name
 * @property string  $description
 * @property string  $disk
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
        'disk',
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
        $disk = $this->disk ?? config('filesystems.public_files');

        // If the disk isn't stored in public (S3 or something),
        // just pass through the URL call
        if ($disk !== 'public') {
            return Storage::disk(config('filesystems.public_files'))
                ->url($this->path);
        }

        // Otherwise, figure out the public URL and save there
        return public_asset(Storage::disk('public')->url($this->path));
    }
}
