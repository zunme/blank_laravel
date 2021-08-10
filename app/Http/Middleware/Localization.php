<?php

namespace App\Http\Middleware;

use Closure, Session;
use Illuminate\Http\Request;

class Localization
{
    protected $languages = ['en','ko','ja'];
    public function handle(Request $request, Closure $next)
    {

        if(!Session::has('locale')) {
            Session::put('locale', $request->getPreferredLanguage($this->languages));
        }
        app()->setLocale(Session::get('locale'));

        return $next($request);
    }
}
