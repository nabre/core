<?php

namespace Nabre\Http\Controllers\User;

use Nabre\Http\Controllers\Controller;

class DashController extends Controller
{
    protected $routeRoot = 'nabre.user.dash';

    function index()
    {
        $content = "Cruscotto";
        return view("Nabre::quick.user", compact('content'));
    }
}
