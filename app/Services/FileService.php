<?php

namespace App\Services;

use App\Interfaces\Service;

/**
 * Class FileService
 * @package App\Services
 */
class FileService extends Service
{
    /**
     * Save a file to disk and return the path
     * @param \Illuminate\Http\UploadedFile $file
     * @param string                        $folder
     * @param string|null                   $disk
     * @return string
     */
    public function saveFile($file, $folder, $disk = null): string
    {
        if (!$disk) {
            $disk = config('filesystems.public_files');
        }

        $path_info = pathinfo($file->getClientOriginalName());
        $filename = str_slug($path_info['filename']).'.'.$path_info['extension'];
        return $file->storeAs($folder, $filename, $disk);
    }
}
