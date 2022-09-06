<?php

namespace Nabre\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class StrArrayCast implements CastsAttributes {


    public function get($model, $key, $value, $attributes)
    {
        $value=array_merge(explode(',',(string)$value),$model->parent->middleware??[]);
        return (array) array_unique(array_filter($value,'strlen'));
    }

    public function set($model, $key, $value, $attributes)
    {
        if(is_array($value)){
            $value='';
        }
        $value=array_diff(array_unique(array_filter(explode(',',(string)$value),'strlen')), $model->parent->middleware??[]);
        sort($value);
        return [$key => implode(',',$value)];
    }
}
