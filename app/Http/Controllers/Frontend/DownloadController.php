<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use App\Models\Airline;
use App\Models\File;
use Auth;
use Flash;
use Illuminate\Support\Facades\Storage;

/**
 * Class DownloadController
 */
class DownloadController extends Controller
{
    /**
     * Show all of the available files
     */
    public function index()
    {
        $airlines = Airline::where('active', 1)->count();
        $files = File::orderBy('ref_model', 'asc')->get();

        /**
         * Group all the files but compound the model with the ID,
         * since we can have multiple files for every `ref_model`
         */
        $grouped_files = $files->groupBy(function ($item, $key) {
            return $item['ref_model'].'_'.$item['ref_model_id'];
        });

        /**
         * The $group here looks like: App\Models\Airport_KAUS
         * Split it into the $class and $id, and then change the
         * name of the group to the object instance "name"
         */
        $regrouped_files = [];
        foreach ($grouped_files as $group => $files) {
            [$class, $id] = explode('_', $group);
            $klass = new $class();
            $obj = $klass->find($id);

            // Check if the object is there first
            if (isset($obj)) {
                $category = explode('\\', $class);
                $category = end($category);

                if ($category == 'Aircraft' && $airlines > 1) {
                    $group_name = $category.' > '.$obj->subfleet->airline->name.' '.$obj->icao.' '.$obj->registration;
                } elseif ($category == 'Aircraft') {
                    $group_name = $category.' > '.$obj->icao.' '.$obj->registration;
                } elseif ($category == 'Airport') {
                    $group_name = $category.' > '.$obj->icao.' : '.$obj->name.' ('.$obj->country.')';
                } elseif ($category == 'Subfleet' && $airlines > 1) {
                    $group_name = $category.' > '.$obj->airline->name.' '.$obj->name;
                } else {
                    $group_name = $category.' > '.$obj->name;
                }

                $regrouped_files[$group_name] = $files;
            }
        }

        ksort($regrouped_files, SORT_STRING);

        return view('downloads.index', [
            'grouped_files' => $regrouped_files,
        ]);
    }

    /**
     * Show the application dashboard
     *
     * @param string $id
     *
     * @return mixed
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\StreamedResponse
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
            return redirect(config('phpvms.login_redirect'));
        }

        $file->download_count++;
        $file->save();

        if ($file->disk === 'public') {
            $storage = Storage::disk('public');
            return $storage->download($file->path, $file->filename);
        }

        // TODO: Config for streamed response?
        return redirect()->to($file->url);
    }
}
