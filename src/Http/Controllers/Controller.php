<?php
namespace Nabre\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


use Illuminate\Routing\Controller as BaseController;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests ;

    function putUri($data=null){
        $keyName = optional($data)->getKeyName();
        $id=optional($data)->$keyName;
        $suffix = is_null($id)?'store':'update';
        $route=$this->getRoute($suffix);
        return route($route,$id);
    }

    protected $routeRoot;
    protected $root;

    function setRoute($suffix,$route=null){
        $route=$route??$this->routeRoot .".". $suffix??null;
        if(\Route::has($route)){
            $this->root[$suffix]=$route;
            return $route;
        }
        return null;
    }

    function getRoute($suffix=null){
        if(is_null($suffix)){
            return $this->routeRoot??null;
        }

        return $this->root[$suffix]??$this->setRoute($suffix)??null;
    }
}
