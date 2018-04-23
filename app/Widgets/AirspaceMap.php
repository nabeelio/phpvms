<?php

namespace App\Widgets;

use App\Interfaces\Widget;

/**
 * Show the live map in a view
 * @package App\Widgets
 */
class AirspaceMap extends Widget
{
    protected $config = [
        'height' => '800px',
        'width'  => '100%',
        'lat' => 0,
        'lon' => 0,
    ];

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function run()
    {
        return view('widgets.airspace_map', [
            'config'    => $this->config,
        ]);
    }
}
