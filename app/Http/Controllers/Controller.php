<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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
        return skin_view($template, $vars, $merge_data);
    }
}
