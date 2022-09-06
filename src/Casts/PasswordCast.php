<?php

namespace Nabre\Casts;

use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class PasswordCast implements CastsAttributes
{

    public function get($model, $key, $value, $attributes)
    {
        return $value;
    }

    public function set($model, $key, $value, $attributes)
    {
        $value = Hash::make($value);

        return [$key => $value];
    }
}
