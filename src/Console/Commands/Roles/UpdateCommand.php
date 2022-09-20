<?php

namespace Nabre\Console\Commands\Roles;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Nabre\Routing\RouteHierarchy;

class UpdateCommand extends Command
{
    protected $signature = 'roles:update';
    protected $description = 'Sync a roles & permission in Route - middleware';

    public function handle()
    {
        $middlewares=collect([]);
        (new RouteHierarchy)->routeGetList()->pluck('name')->each(function($name)use(&$middlewares){
            $middlewares=$middlewares->merge(\Route::getRoutes()->getByName($name)->middleware())->unique()->sort()->values();
        });
        $roles=$middlewares->like(null,'role:%')->values();
        $permissions=$middlewares->like(null,'permission:%')->values();
        collect(compact('roles','permissions'))->each(function($i,$type){

        });
    }
}
