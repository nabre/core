<?php

namespace Nabre\Repositories\Form;


class Validator{

    use StructureNavigateTrait;

    var $data;
    var $method;
    static $create='POST';
    static $update='PUT';

    function saveIn($data=null){
        $this->data($data);
        $request=request();

        /*
        $method=$this->method();
        $vars=collect($request->all());
        $varsName=$vars->keys();
        $requestValidateParam=$this->elements()->filter(function($it)use($varsName,$method){
            return in_array($it['variable'],$varsName->toArray()) || in_array('required',(array)($it['set']['request'][$method]??null));
        })->pluck('set.request.'.$method,'variable')->toArray();

        $vars=$request->validate( $requestValidateParam );
        */
        

        $vars=$request->all();

        return $this->data->recursiveSave($vars);
    }

    private function data($data = null)
    {
        $collection = $this->collection();
        if (!($data instanceof $collection)) {
            $data = new $collection;
        }
        $this->method($data);
        $this->data = $data;

        return $this;
    }

    private function method($data = null)
    {
        if (!is_null($data)) {
            $key = $data->{$data->getKeyName()};
            $this->method = is_null($key) ? self::$create : self::$update;
        }
        return $this->method;
    }
}
