<?php

namespace Nabre\Casts;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CkeditorCast implements CastsAttributes
{

    public function get($model, $key, $value, $attributes)
    {
        return $value;
    }

    public function set($model, $key, $value, $attributes)
    {
        $value =  preg_replace("/<script.*?\/script>/s", "", $value) ? : $value;

        return [$key => $value];
    }
}
