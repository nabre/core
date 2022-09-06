<?php

namespace Nabre\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Exception;
use Nabre\Repositories\Pages;
use Illuminate\Support\Arr;

class SettingOverrideMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $override = config('setting.override', []);
        foreach (Arr::dot($override) as $config_key => $setting_key) {
            $config_key = is_string($config_key) ? $config_key : $setting_key;

            try {
                if (!is_null($value = setting($setting_key))) {
                    config([$config_key => $value]);
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        return $next($request);
    }
}
