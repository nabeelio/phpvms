<?php

namespace App\Http\Controllers;


class HomeController extends AppBaseController
{
    /**
     * Show the application dashboard.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->view('home');
    }
}
