<?php

namespace Nabre\Providers;

use Collective\Html\HtmlFacade as Html;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;
use Nabre\Repositories\Form\Define;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('like', function ($key, $string) {
            return $this->filter(function ($item) use ($key, $string) {
                $value = data_get($item, $key);

                $bool = true;
                $bPos = 0;
                $parts = collect(explode("%", $string));

                $first = !is_null($parts->first());
                $last = is_null($parts->last());

                $parts = $parts->filter()->values();
                $parts->each(function ($find, $p) use (&$bool, &$bPos, $value, $first) {
                    if ($bool) {
                        $pos = strpos($value, $find, $bPos);
                        if ($pos === false || ($pos < $bPos && $pos != 0) || (!$p && $first && $pos)) {
                            $bool = false;
                        }
                    }
                });

                if ($bool && $last) {
                    $find = (string)$parts->last();
                    $bool = $bPos == strlen($value) - strlen($find);
                }

                return $bool;
            });
        });

        Collection::macro('notLike', function ($key, $string) {
            return $this->reject(function ($item) use ($key, $string) {
                return $item->like($key, $string);
            });
        });

        Collection::macro('policy', function ($fn, $class) {
            return $this->filter(function ($i) use ($fn, $class) {
                return \Auth::user()->can($fn, [$class, $i]);
            });
        });

        Collection::macro('sortPages', function () {
            return $this->sortBy(['root', 'def_page', 'uri'])->values();
        });

        Collection::macro('existClass', function ($key = null) {
            return $this->filter(function ($string) use ($key) {
                $string = is_null($key) ? $string : data_get($string, $key);
                return class_exists($string);
            })->values();
        });

        Route::macro('getRoutesList', function () {
            $routes = collect(Route::getRoutes())->map(function ($route) {
                return (object)[
                    'host'       => $route->domain(),
                    'method'     => $route->methods(),
                    'uri'        => $route->uri(),
                    'name'       => $route->getName(),
                    'action'     => $route->getActionName(),
                    //'middleware' => $route->middleware(),
                ];
            })->sortBy('uri')->values();
            return $routes;
        });

        Html::macro('div', function ($content = null, $options = []) {
            return Html::tag('div', $content, $options);
        });

        Html::macro('a', function ($content = null, $options = []) {
            return Html::tag('a', $content, $options);
        });

        Html::macro('button', function ($content = null, $options = []) {
            return Html::tag('button', $content, $options);
        });

        Html::macro('btn', function ($content = null, $options = []) {
            return Html::button($content, $options);
        });

        Html::macro('table', function ($content = null, $options = []) {
            return Html::tag('table', $content, $options);
        });

        Html::macro('tr', function ($content = null, $options = []) {
            return Html::tag('tr', $content, $options);
        });

        Html::macro('thead', function ($content = null, $options = []) {
            return Html::tag('thead', $content, $options);
        });

        Html::macro('tbody', function ($content = null, $options = []) {
            return Html::tag('tbody', $content, $options);
        });

        Html::macro('tfoot', function ($content = null, $options = []) {
            return Html::tag('tfoot', $content, $options);
        });

        Html::macro('caption', function ($content = null, $options = []) {
            return Html::tag('caption', $content, $options);
        });

        Html::macro('th', function ($content = null, $options = []) {
            return Html::tag('th', $content, $options);
        });

        Html::macro('td', function ($content = null, $options = []) {
            return Html::tag('td', $content, $options);
        });
    }
}
