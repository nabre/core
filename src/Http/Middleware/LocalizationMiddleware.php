<?php

namespace Nabre\Http\Middleware;

use Closure;
use Nabre\Repositories\LocalizationRepositorie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $localization=new LocalizationRepositorie;
        $localization->localeMiddleware();

        return $next($request);
    }
}
