<?php

namespace Modules\Sample\Http\Controllers;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;


class SampleController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('sample::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sample::create');
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
        return view('sample::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        return view('sample::edit');
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
