<?php

namespace Nabre\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Nabre\Repositories\Pages;

class DefaultAccountMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $redirect='/user/profile';
        if(get_data(auth()->user(),'email')==config('auth.adminaccountdefault.email') && $redirect!=request()->path()){
            return redirect($redirect);
        }
        return $next($request);
    }
}
