<?php

namespace App\Http\Controllers\Frontend;

use App\Interfaces\Controller;
use App\Models\File;
use Auth;
use Flash;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileController
 * @package App\Http\Controllers\Frontend
 */
class FileController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function show($id)
    {
        /**
         * @var File $file
         */
        $file = File::find($id);
        if (!$file) {
            Flash::error('File doesn\'t exist');
            return redirect()->back();
        }

        // Allowed to download? If not, direct to login
        if (!$file->public && !Auth::check()) {
            return redirect(config('app.login_redirect'));
        }

        ++$file->download_count;
        $file->save();

        if($file->disk === 'public') {
            $storage = Storage::disk('public');
            return $storage->download($file->path, $file->filename);
        }

        // TODO: Config for streamed response?
        return redirect()->to($file->url);
    }
}
