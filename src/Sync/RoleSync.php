<?php

namespace Nabre\Sync;

use Nabre\Models\Role as Model;
use Nabre\Repositories\Pages;
use Nabre\Routing\RouteHierarchy;
use Nabre\Services\AutoSync;

class RoleSync extends AutoSync
{
    var $model = Model::class;

    function current()
    {
        return $this->model::where('guard_name', 'web')->get();
    }

    function exists()
    {
        $middlewares=collect([]);
        (new RouteHierarchy)->routeGetList()->pluck('name')->each(function($name)use(&$middlewares){
            $middlewares=$middlewares->merge(\Route::getRoutes()->getByName($name)->middleware())->unique()->sort()->values();
        });
        $roles=$middlewares->like(null,'role:%')->map(function($string){
            list(,$name)=explode(":",$string);
            return $name;
        })->values();
        return $roles->map(function ($name) {
            $data = compact('name');
            data_set($data,'guard_name','web');
            return $data;
        });
    }

    function put($data)
    {
        return $this->model::where('name', $data['name'])->firstOrCreate();
    }
}
