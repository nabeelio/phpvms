<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\Aircraft;
use App\Models\Airport;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Class DownloadController
 * @package App\Http\Controllers\Admin
 */
class DownloadController extends Controller
{
    /**
     * @var FileService
     */
    private $fileSvc;

    /**
     * DownloadController constructor.
     * @param FileService $fileSvc
     */
    public function __construct(FileService $fileSvc)
    {
        $this->fileSvc = $fileSvc;
    }

    /**
     * Downloads Index Page
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $downloads = File::orderBy('ref_model', 'asc')->get();
        return view('admin.downloads.index', [
            'downloads' => $downloads,
        ]);
    }

    /**
     * Create Page for Downloads
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $download = null;
        $ref_models = [
            'App\Models\Aircraft'  => 'Aircraft',
            'App\Models\Airport'   => 'Airport',
            ''                     => 'Others',
        ];
        $ref_aircrafts = Aircraft::all()->pluck('icao', 'id')->toArray();
        $ref_airports = Airport::all()->pluck('id', 'id')->toArray();
        return view('admin.downloads.create', [
            'download'      => $download,
            'ref_models'    => $ref_models,
            'ref_aircrafts' => $ref_aircrafts,
            'ref_airports'  => $ref_airports,
        ]);
    }

    /**
     * Store the Download into our System
     *
     * @param Request $request
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $attrs = $request->all();
        $validator = Validator::make(
            $attrs,
            [
                'name'        => 'required',
                'description' => 'nullable',
                'file'        => 'nullable|file|mimes:zip,pdf,jpeg,bmp,png',
                'url'         => 'nullable|url',
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

        flash()->success('Download saved successfully');

        return redirect(route('admin.downloads.index'));
    }

    /**
     * Edit the Download
     *
     * @param $id
     *
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $download = File::find($id);
        $ref_models = [
            'App\Models\Aircraft'  => 'Aircraft',
            'App\Models\Airport'   => 'Airport',
            ''                     => 'Others',
        ];
        $ref_aircrafts = Aircraft::all()->pluck('icao', 'id')->toArray();
        $ref_airports = Airport::all()->pluck('id', 'id')->toArray();
        return view('admin.downloads.edit', [
            'download'      => $download,
            'ref_models'    => $ref_models,
            'ref_aircrafts' => $ref_aircrafts,
            'ref_airports'  => $ref_airports,
        ]);
    }

    /**
     * Update the Download.
     *
     * @param $id
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function update($id, Request $request)
    {
        $update = $this->fileSvc->update($id, $request->all());
        if ($update) {
            flash()->success('Download Edited successfully');
        } else {
            flash()->error('Download Editing Failed');
        }
        return redirect()->back();
    }

    /**
     * Remove the Download from storage.
     *
     * @param $id
     *
     * @throws \Exception
     *
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $file = File::find($id);
        if (!$file) {
            flash()->error('Download doesn\'t exist');
            return redirect()->back();
        }

        $this->fileSvc->removeFile($file);

        flash()->success('Download deleted successfully.');

        return redirect()->back();
    }
}
