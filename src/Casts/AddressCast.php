<?php

namespace Nabre\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class AddressCast implements CastsAttributes {

    public function get($model, $key, $value, $attributes)
    {
        $value=(array)$value;
        $out='<address>';
        $out.=trim((is_null($value['street'])?null: $value['street']).(is_null($value['num'])?null: ", ".$value['num']).'<br>');
        $out.=trim((is_null($value['cap'])  ?null: $value['cap']) .(is_null($value['city'])  ?null: " ".$value['city'])  .'<br>');
        $out.='</address>';
        return $out;
    }

    public function set($model, $key, $value, $attributes)
    {
        $value=array_filter((array)$value, 'strlen');

        if(!count($value)){
            $value=null;
        }

        return [$key => $value];
    }
}
