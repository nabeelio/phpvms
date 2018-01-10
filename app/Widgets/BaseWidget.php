<?php
/**
 * Created by IntelliJ IDEA.
 * User: nshahzad
 * Date: 1/8/18
 * Time: 7:46 PM
 */

namespace App\Widgets;

use Arrilot\Widgets\AbstractWidget;

class BaseWidget extends AbstractWidget
{
    public $cacheTime = 0;

    public function view(string $template, array $vars = [])
    {
        $tpl = 'layouts/' . config('phpvms.skin') . '/' . $template;
        return view($tpl, $vars);
    }
}
