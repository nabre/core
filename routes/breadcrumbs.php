<?php

use Nabre\Models\Page;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Nabre\Routing\RouteHierarchy;


Breadcrumbs::macro('resource', function (string $name, string $title, string $parent) {
    Breadcrumbs::for("{$name}.index", function (BreadcrumbTrail $trail) use ($name, $title, $parent) {
        if (!is_null($parent)) {
            $trail->parent($parent);
        }
        $trail->push($title, route("{$name}.index"));
    });

    Breadcrumbs::for("{$name}.create", function (BreadcrumbTrail $trail) use ($name) {
        $trail->parent("{$name}.index");
        $trail->push(__('Nabre::resource.Create'));
    });

    Breadcrumbs::for("{$name}.show", function (BreadcrumbTrail $trail,$data) use ($name) {
        $trail->parent("{$name}.index");
        $url=\Route::has($name.'.show')?route($name.'.show',data_get($data,'id')):null;
        $trail->push(data_get($data,'show_string')??data_get($data,'id'),$url);
    });

    Breadcrumbs::for("{$name}.edit", function (BreadcrumbTrail $trail,$data) use ($name) {
        $trail->parent("{$name}.show",$data);
        $trail->push(__('Nabre::resource.Edit'));
    });
});

Breadcrumbs::macro('title', function () {
    return ($breadcrumb = Breadcrumbs::current()) ? "{$breadcrumb->title} " : null;
});

Breadcrumbs::macro('pageTitle', function () {
  /*  $title = ($breadcrumb = Breadcrumbs::title()) ? $breadcrumb." - ":null;

    if (($page = (int) request('page')) > 1) {
        $title .= "Page $page – ";
    }*/

    return  config('app.name', 'Laravel') ;
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

$folderList = (new RouteHierarchy)->folderList();

collect(\Route::getRoutesList())->filter(function ($i) use ($rejectClasses) {
    return in_array($i->uri, (new RouteHierarchy)->all()->toArray())
        && in_array('GET', $i->method)
        && (strpos($i->name, '::') === false
            && strpos($i->name, 'ignition.') === false
            && strpos($i->name, 'sanctum.') === false)
        && !in_array($i->action, $rejectClasses->toArray());
})->sortBy('uri')->each(function ($i) use ($folderList) {
    $parent = null;
    $uri = $i->uri;
    $folderList->filter(function ($f) use ($uri) {
        $pos = strpos($uri, $f);
        return $pos !== false && $pos == 0;
    })->map(function ($uri) {
        $name = str_replace("/", ".", $uri);
        return (object) get_defined_vars();
    })->push($i)->sortBy('uri')->values()->each(function ($i) use (&$parent) {
        $name = (string)$i->name;
        if (!Breadcrumbs::exists($name)) {

            $string = optional(Page::where('name',$name)->first())->string ?? collect(explode("/", $i->uri))->last();
            $string = ucfirst(strtolower($string));
            $uri = \Route::has($name) ? route($name) : url($i->uri);

            $isResource = strpos($i->action ?? null, "@index") !== false;
            if ($isResource) {
                $name = str_replace(".index", "", $name);
                Breadcrumbs::resource($name, $string, $parent);
            } elseif(!Breadcrumbs::exists($name)) {
               Breadcrumbs::for($name, function (BreadcrumbTrail $trail) use ($string, $parent, $uri) {
                    if (!is_null($parent)) {
                        $trail->parent($parent);
                    }
                    $trail->push($string, $uri);
                });
            }
        }
        $parent = $name;
    });
});
