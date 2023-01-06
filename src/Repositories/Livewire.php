<?php

namespace Nabre\Repositories;

use Illuminate\Support\Str;

class Livewire
{
    static function load($fn,array $params = [], $id = null)
    {
        $id = $id ?? Str::random(40);
        return view('Nabre::paginate.skeleton.code.livewire', get_defined_vars());
    }
}
