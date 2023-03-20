<?php

namespace App\Http\Controllers\Frontend;

use App\Contracts\Controller;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        $cookie = Cookie::make('lang', $lang, 60 * 24 * 365);
        return back()->withCookie($cookie);
    }
}
