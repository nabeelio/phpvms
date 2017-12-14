<?php

namespace Modules\Installer\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;


class InstallerController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('installer::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('installer::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     */
    public function show()
    {
        return view('installer::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        return view('installer::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
    }
}
