<?php

namespace Nabre\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
//use Illuminate\View\Component;

class ImpersonateMiddleware
{
    /**
     * Handle an incoming request.
     */
    function handle(Request $request, Closure $next)
    {
        if (!is_null(\Auth::user()) && $request->session()->has('impersonate')) {
            $id = $request->session()->get('impersonate');
            \Auth::onceUsingID($id);
        }
        return $next($request);
    }
}
