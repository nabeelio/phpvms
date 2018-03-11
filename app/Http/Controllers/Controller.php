<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a view but pull it from the active skin
     * @param string $template
     * @param array $vars
     * @param array $merge_data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view($template, array $vars=[], $merge_data=[])
    {
        return view($template, $vars, $merge_data);
    }
}
