<?php

namespace App\Http\Controllers\Admin;

use App\Interfaces\Controller;
use App\Models\File;
use App\Services\FileService;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Log;
use Validator;

/**
 * Class FilesController
 * @package App\Http\Controllers\Admin
 */
class FilesController extends Controller
{
    private $fileSvc;

    public function __construct(FileService $fileSvc)
    {
        $this->fileSvc = $fileSvc;
    }

    /**
     * Store a newly file
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $attrs = $request->post();

        // Not using a form validation here because when it redirects,
        // it leaves the parent forms all blank, even though it goes
        // back to the right place. So just manually validate
        $validator = Validator::make($request->all(), [
            'filename' => 'required',
            'file'     => 'required|file'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput(Input::all())->withErrors($validator);
        }

        Log::info('Uploading files', $attrs);

        $file = $request->file('file');
        $file_path = $this->fileSvc->saveFile($file, 'files');

        $asset = new File();
        $asset->name = $attrs['filename'];
        $asset->disk = config('filesystems.public_files');
        $asset->path = $file_path;
        $asset->public = true;
        $asset->ref_model = $attrs['ref_model'];
        $asset->ref_model_id = $attrs['ref_model_id'];

        $asset->save();

        Flash::success('Files uploaded successfully.');
        return redirect()->back();
    }

    /**
     * Remove the file from storage.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $file = File::find($id);
        if (!$file) {
            Flash::error('File doesn\'t exist');
            return redirect()->back();
        }

        Storage::disk(config('filesystems.public_files'))->delete($file->path);
        $file->delete();

        Flash::success('File deleted successfully.');
        return redirect()->back();
    }
}
