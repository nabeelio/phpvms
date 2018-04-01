<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CreateFilesRequest;
use App\Interfaces\Controller;
use App\Models\File;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Log;

/**
 * Class FilesController
 * @package App\Http\Controllers\Admin
 */
class FilesController extends Controller
{
    /**
     * Store a newly file
     * @param CreateFilesRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CreateFilesRequest $request)
    {
        $attrs = $request->post();

        Log::info('Uploading files', $attrs);

        /**
         * @var $file  \Illuminate\Http\UploadedFile
         */
        $file = $request->file('file');
        $file_path = $file->storeAs(
            'files',
            $file->getClientOriginalName(),
            config('filesystems.public_files')
        );

        $asset = new File();
        $asset->name = $attrs['name'];
        $asset->path = $file_path;
        $asset->public = true;
        $asset->ref_model = $attrs['ref_model'];
        $asset->ref_model_id = $attrs['ref_model_id'];

        $asset->save();

        /*foreach($files as $file) {
            $file_path = $file->store('files', $file->getClientOriginalName(), 'public');
        }*/

        # Where do we go now? OooOOoOoOo sweet child
        $redirect = $attrs['redirect'];
        if(!$redirect) {
            $redirect = route('admin.dashboard.index');
        }

        Flash::success('Files uploaded successfully.');
        return redirect($redirect);
    }

    /**
     * Remove the file from storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function delete($id, Request $request)
    {
        $redirect = $request->post('redirect');
        if (!$redirect) {
            $redirect = route('admin.dashboard.index');
        }

        $file = File::find($id);
        if (!$file) {
            Flash::error('File doesn\'t exist');
            return redirect($redirect);
        }

        Storage::disk(config('filesystems.public_files'))->delete($file->path);
        $file->delete();

        Flash::success('File deleted successfully.');
        return redirect($redirect);
    }
}
