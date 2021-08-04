<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLevel
{
    public function handle(Request $request, Closure $next, $role)
    {
        switch ($role) {
            case 'admin':
            case 'user':
                if (auth()->user()->user_level < config("ext.user.user_level.roles.{$role}.level")) {
                    return redirect('/');
                }
                break;
            default:
                return redirect('/');
                break;
        }

        return $next($request);
    }
}
