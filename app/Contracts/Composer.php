<?php

namespace App\Contracts;

use Illuminate\View\View;

abstract class Composer
{
    abstract public function compose(View $view);
}
