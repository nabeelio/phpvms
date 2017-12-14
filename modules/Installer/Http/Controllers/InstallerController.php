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

}
