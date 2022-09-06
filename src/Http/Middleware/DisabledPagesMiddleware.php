<?php

namespace Nabre\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Nabre\Repositories\Pages;

class DisabledPagesMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $name=$request->route()->getName();
        if(Pages::isDisabled($name)){
            abort(404);
        }
        return $next($request);
    }
}
