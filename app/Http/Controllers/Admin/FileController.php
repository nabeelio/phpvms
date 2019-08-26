<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Http\Requests\FileUploadRequest;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laracasts\Flash\Flash;

/**
 * Class FileController
 */
class FileController extends Controller
{
    private $fileSvc;

    public function __construct(FileService $fileSvc)
    {
        $this->fileSvc = $fileSvc;
    }

    /**
     * Store a newly file
     *
     * @param Request $request
     *
     * @throws \Hashids\HashidsException
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $attrs = $request->post();

        /*
         * Not using a form validation here because when it redirects, it leaves
         * the parent forms all blank, even though it goes back to the right place.
         *
         * The fields are also named file_name and file_description so that if there
         * are validation errors, the flash messages  doesn't conflict with other
         * fields on the page that might have the "name" and "description" fields
         */
        $validator = Validator::make(
            $request->all(),
            [
                'file_name'        => 'required',
                'file_description' => 'nullable',
                'file'             => [
                    Rule::requiredIf(function () {
                        return ! request()->filled('url');
                    }),
                    'file',
                ],
                'url'              => [
                    Rule::requiredIf(function () {
                        return !request()->hasFile('file');
                    }),
                    'url',
                ],
            ],
            [
                'file.required' => 'File or URL are required',
                'url.required'  => 'File or URL are required',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput(Input::all());
        }

        Log::info('Uploading files', $attrs);

        $attrs['name'] = $attrs['file_name'];
        $attrs['description'] = $attrs['file_description'];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $this->fileSvc->saveFile($file, 'files', $attrs);
        }

        // Didn't provide a file to upload, just a URL to a file
        // Create the model directly and just associate that
        else if($request->filled('url')) {
            $file = new File($attrs);
            $file->path = $attrs['url'];
            $file->save();
        }

        Flash::success('Files saved successfully');

        return redirect()->back();
    }

    /**
     * Remove the file from storage.
     *
     * @param $id
     *
     * @throws \Exception
     *
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
