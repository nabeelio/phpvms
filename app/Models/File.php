<?php

namespace App\Models;

use App\Contracts\Model;
use App\Models\Traits\HashIdTrait;
use App\Models\Traits\ReferenceTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property string $name
 * @property string $description
 * @property string $disk
 * @property string $path
 * @property bool   $public
 * @property int    $download_count
 * @property string $url
 * @property string $filename
 */
class File extends Model
{
    use HashIdTrait;
    use ReferenceTrait;

    public $table = 'files';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'description',
        'disk',
        'path',
        'public',
        'ref_model',
        'ref_model_id',
    ];

    protected $casts = [
        'public' => 'boolean',
    ];

    public static $rules = [
        'name' => 'required',
    ];

    private $pathinfo;

    /**
     * Return the file extension
     *
     * @return Attribute
     */
    public function extension(): Attribute
    {
        return Attribute::make(
            get: function ($_, $attrs) {
                if (!$this->pathinfo) {
                    $this->pathinfo = pathinfo($this->path);
                }

                return $this->pathinfo['extension'];
            }
        );
    }

    /**
     * Get just the filename
     *
     * @return Attribute
     */
    public function filename(): Attribute
    {
        return Attribute::make(
            get: function ($_, $attrs) {
                if (!$this->pathinfo) {
                    $this->pathinfo = pathinfo($this->path);
                }

                return $this->pathinfo['filename'].'.'.$this->pathinfo['extension'];
            }
        );
    }

    /**
     * Get the full URL to this attribute
     *
     * @return Attribute
     */
    public function url(): Attribute
    {
        return Attribute::make(
            get: function ($_, $attrs) {
                if (Str::startsWith($this->path, 'http')) {
                    return $this->path;
                }

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
        );
    }
}
