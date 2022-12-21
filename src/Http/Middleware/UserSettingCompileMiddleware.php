<?php

namespace Nabre\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Nabre\Repositories\Pages;

class UserSettingCompileMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {

        if (!Pages::userSettingCompile()) {
            abort(404);
        }

        return $next($request);
    }
}
