<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;

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
        $attrs = $request->all();
        if (!$request->has('file_name')) {
            $attrs['file_name'] = $request->input('name');
        }
        if (!$request->has('file_description')) {
            $attrs['file_description'] = $request->input('description');
        }
        /*
         * Not using a form validation here because when it redirects, it leaves
         * the parent forms all blank, even though it goes back to the right place.
         *
         * The fields are also named file_name and file_description so that if there
         * are validation errors, the flash messages  doesn't conflict with other
         * fields on the page that might have the "name" and "description" fields
         *
         * Was also going to use the "required_without" rule, but that doesn't appear
         * to work properly with a file upload
         */
        $validator = Validator::make(
            $attrs,
            [
                'file_name'        => 'required',
                'file_description' => 'nullable',
                'file'             => 'nullable|file|mimes:zip,pdf,jpeg,bmp,png',
                'url'              => 'nullable|url',
                /*'file'             => [
                    Rule::requiredIf(function () {
                        return request()->filled('url') === false;
                    }),
                    'file',
                ],
                'url' => [
                    Rule::requiredIf(function () {
                        return request()->hasFile('file') === false;
                    }),
                    'url',
                ],*/
            ],
            [
                'file.required' => 'File or URL are required',
                'url.required'  => 'File or URL are required',
            ]
        );

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator->errors())
                ->withInput($request->all());
        }

        if (!$request->hasFile('file') && !$request->filled('url')) {
            $validator->errors()->add('url', 'A URL or file must be uploaded!');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
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
        elseif ($request->filled('url')) {
            $file = new File($attrs);
            $file->path = $attrs['url'];
            $file->save();
        }

        flash()->success('Files saved successfully');

        return redirect()->back();
    }

    /**
     * Update the file in storage.
     *
     * @param $id
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id, Request $request)
    {
        $update = $this->fileSvc->update($id, $request->all());
        if ($update) {
            flash()->success('File Edited successfully');
        } else {
            flash()->error('File Editing Failed');
        }
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
            flash()->error('File doesn\'t exist');
            return redirect()->back();
        }

        $this->fileSvc->removeFile($file);

        flash()->success('File deleted successfully.');

        return redirect()->back();
    }
}
