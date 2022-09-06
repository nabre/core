<?php

namespace Nabre\Routing;

use Nabre\Repositories\Menu\Generate;

class RouteHierarchy
{

    function routeGetList()
    {
        return \Route::getRoutesList()->filter(function ($r) {
            $varOptional = count(explode("{", $r->uri)) == count(explode("{?", $r->uri)) || count(explode("{", $r->uri)) == 0;
            return in_array('GET', (array)$r->method) && $varOptional;
        })->values();
    }

    function ruoteUri($middleware = false)
    {
        return $this->routeGetList()->pluck('uri')->sortBy('uri')->values();
    }

    function folderList()
    {
        $folder = collect([]);
        $routes = $this->ruoteUri();
        $routes->each(function ($str) use (&$folder) {
            $dir = [];
            collect(explode("/", $str))->each(function ($i) use (&$dir, &$folder) {
                $dir[] = $i;
                $folder = $folder->push(implode("/", $dir));
            });
        });
        return $folder->reject(function ($str) use ($routes) {
            return in_array($str, $routes->toArray());
        })->unique()->filter()->sort()->values();
    }

    function all()
    {
        $folders = $this->folderList();
        return $this->ruoteUri()->merge($folders)->sort()->values();
    }

    function routeRedirect(&$redirect)
    {
        $uri = implode("/", request()->segments());

        $redirect=Generate::redirect($uri);
        $redirect = $redirect ?? $this->ruoteUri()->sort()->values()->filter(function ($str) use ($uri) {
            $pos = strpos($str, $uri);
            return $pos !== false && $pos == 0;
        })->reject(function ($str) use ($uri) {
            return $str == $uri;
        })->values()->first();

        if (!is_null($redirect)) {
            return true;
        }

        return false;
    }

    function pagesSync()
    {
        $folderList = $this->folderList()->map(function ($uri) {
            $name = str_replace("/", ".", $uri);
            return (object) get_defined_vars();
        });

        $rejectClasses = collect([]);
        collect(\Route::getRoutesList())->filter(function ($i) {
            return strpos($i->action, '@index') !== false;
        })->pluck('action')->map(function ($action) {
            list($class, $method) = explode('@', $action);
            return $class;
        })->each(function ($class) use (&$rejectClasses) {
            $list = collect(\Route::getRoutesList())->filter(function ($i) use ($class) {
                return strpos($i->action, $class) !== false && strpos($i->action, '@index') === false;
            })->pluck('action');
            $rejectClasses = $rejectClasses->merge($list);
        });


        $pages = collect(\Route::getRoutesList())->filter(function ($i) use ($rejectClasses) {
            return in_array($i->uri, (new RouteHierarchy)->all()->toArray())
                && in_array('GET', $i->method)
                && !in_array($i->action, $rejectClasses->toArray());
        })->merge($folderList)->filter(function ($i) {
            return !is_null($i->name) && (strpos($i->name, '::') === false
                && strpos($i->name, 'ignition') === false
                && strpos($i->name, 'livewire') === false
                && (strpos($i->name, 'api') === false || strpos($i->name, 'api') != 0)
                && strpos($i->name, 'sanctum') === false);
        });

        return $pages;
    }
}
