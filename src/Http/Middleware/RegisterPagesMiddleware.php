<?php

namespace Nabre\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Nabre\Repositories\Pages;

class RegisterPagesMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        if(Pages::isDisabled('register')){
            abort(404);
        }

        return $next($request);
    }
}
