<?php

namespace Nabre\Casts;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ImageCodeCast implements CastsAttributes
{

    public function get($model, $key, $value, $attributes)
    {
        $value = base64_decode($value);

        return $value;
    }

    public function set($model, $key, $value, $attributes)
    {
        $value = base64_encode($value);

        return [$key => $value];
    }
}
