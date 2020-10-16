<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Controller;
use App\Models\Airport;
use App\Models\Aircraft;
use App\Models\File;

class DownloadController extends Controller
{
    public function index()
    {
        $downloads = File::orderBy('ref_model', 'asc')->get();;
        return view('admin.downloads.index', [
            'downloads' => $downloads
        ]);
    }

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
            'ref_airports'  => $ref_airports
        ]);
    }

    public function edit($id) {
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
            'ref_airports'  => $ref_airports
        ]);
    }
}
