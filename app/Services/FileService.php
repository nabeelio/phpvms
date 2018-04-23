<?php

namespace App\Services;

use App\Interfaces\Service;
use App\Models\File;

/**
 * Class FileService
 * @package App\Services
 */
class FileService extends Service
{
    /**
     * Save a file to disk and return a File asset
     * @param \Illuminate\Http\UploadedFile $file
     * @param string                        $folder
     * @param array                         $attrs
     * @return File
     * @throws \Hashids\HashidsException
     */
    public function saveFile($file, $folder, array $attrs)
    {
        $attrs = array_merge([
            'name'         => '',
            'description'  => '',
            'public'       => false,
            'ref_model'    => '',
            'ref_model_id' => '',
            'disk'         => config('filesystems.public_files'),
        ], $attrs);

        $id = File::createNewHashId();
        $path_info = pathinfo($file->getClientOriginalName());

        # Create the file, add the ID to the front of the file to account
        # for any duplicate filenames, but still can be found in an `ls`

        $filename = $id . '_'
            . str_slug(trim($path_info['filename']))
            . '.' . $path_info['extension'];

        $file_path = $file->storeAs($folder, $filename, $attrs['disk']);

        $asset = new File($attrs);
        $asset->id = $id;
        $asset->path = $file_path;
        $asset->save();

        return $asset;
    }
}
