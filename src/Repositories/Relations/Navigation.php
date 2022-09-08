<?php

namespace Nabre\Repositories\Relations;

use App\Models\User;

class Navigation{

    var $baseValues;

    function __construct()
    {
        $this->baseValues=[
            User::class=>auth()->user()->id,
        ];
    }
}
