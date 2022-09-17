<?php

namespace Nabre\Repositories\Menu;

use Nabre\Models\Menu;
use Nabre\Models\Page;

class Generate
{
    static function name($name, $mode = 'side')
    {
        $menu = Menu::get()->where('name', $name)->first();
        if (is_null($menu)) {
            return "menu: '{$name}' non esiste!";
        }
        if ($menu->auto) {
            $pages = self::autoLoadPages($menu->page->uri);
        } else {
            $pages = $menu->items->pluck('page')->filter();
        }

        self::pagesMiddleware($pages);

        $items = ($menu->auto || ($menu->tree ?? false)) ? self::tree($pages) : self::singleLevel($pages);

        $array = self::buildRecursiveArray($items->unique());

        if (!$array->pluck('sub')->filter()->count()) {
            $array = collect([['i' => null, 'sub' => $array]]);
        }

        switch ($mode) {
            case "side":
                return (new SideBar($menu))->build($array);
                break;
            case "top":
                return (new TopBar($menu))->build($array);
                break;
            default:
                return "Mode '" . $mode . "' non identificato";
                break;
        }
    }

    static function redirect($uri)
    {
        $pages = self::autoLoadPages($uri);
        self::pagesMiddleware($pages);
        return optional($pages->sortPages()->first())->uri ?? null;
    }

    static private function pagesMiddleware(&$pages)
    {
        $pages = $pages->reject(function ($page) {
            return (bool)($page->disabled ?? false);
        })->filter(function ($page) {
            switch ($page->folder ?? false) {
                case true;
                    $pages = self::autoLoadPages($page->uri);
                    self::pagesMiddleware($pages);
                    return (bool) $pages->count();
                    break;
                case false;
                    return self::pageMiddlewareCheck($page->name);
                    break;
            }
            return true;
        })->values();
    }

    static function pageMiddlewareCheck($name)
    {
        $middleware = \Route::getRoutes()->getByName($name);

        if (is_null($middleware)) {
            return false;
        }

        $user = \Auth::user();
        foreach ($middleware->middleware() as $mid) {
            @list($auth, $name) = explode(":", $mid);
            switch ($auth) {
                case "auth":
                    if (!\Auth::check()) {
                        return false;
                    }
                    break;
                case "verified":
                    if (is_null($user) || !$user->hasVerifiedEmail()) {
                        return false;
                    }
                    break;
                case "role":
                    if (is_null($user) || !$user->hasAnyRole($name)) {
                        return false;
                    }
                    break;
                case "permission":
                    if (is_null($user) || !$user->hasPermissionTo($name)) {
                        return false;
                    }
                    break;
                case "guest":
                    if (\Auth::check()) {
                        return false;
                    }
                    break;
                case "web":
                    break;
                case "registration":

                    break;
                case "abort":
                    if (!in_array($name, [401, 403, 200])) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }

    static function autoLoadPages($uri)
    {
        return Page::where('folder', false)->where('uri', 'like', $uri . '%')->get()->sortBy('uri')->values();
    }

    static function buildRecursiveArray($items, $find = null)
    {
        $lvl = $items->pluck('lvl')->unique()->min();
        $result = $items->where('lvl', $lvl);
        $result = self::findRoot($result, $find);
        $array = $result->map(function ($i) use ($items, $lvl) {
            if ($i->folder ?? false) {
                $find = $i->uri;
                $result = $items->where('lvl', ">", $lvl);
                $result = self::findRoot($result, $find);
                $sub = self::buildRecursiveArray($result, $find);
                if (!$sub->count()) {
                    $sub = false;
                }
            } else {
                $sub = false;
            }

            return compact('i', 'sub');
        });

        return $array;
    }

    static function findRoot($items, $find = null)
    {
        if (!is_null($find)) {
            return $items->filter(function ($i) use ($find) {
                $pos = strpos($i->uri, $find);
                return $pos !== false && $pos == 0;
            });
        }

        return $items;
    }

    static function tree($pages)
    {
        $array = collect([]);
        $pages->pluck('uri')->each(function ($uri) use (&$array) {
            $tmp = [];
            collect(explode('/', $uri))->each(function ($p) use (&$tmp, &$array) {
                $tmp[] = $p;
                $array->push(implode("/", $tmp));
            });
        });
        $folders = Page::where('folder', true)->whereIn('uri', $array->unique()->sort()->values()->toArray())->get();

        return $pages->merge($folders)->sortPages();
    }

    static function singleLevel($pages)
    {
        $pages = $pages->map(function ($i) {
            $i->folder = false;
            return $i;
        });
        return $pages;
    }
}
