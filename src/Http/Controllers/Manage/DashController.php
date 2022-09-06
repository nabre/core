<?php

namespace Nabre\Http\Controllers\Manage;

use Nabre\Http\Controllers\Controller;

class DashController extends Controller
{
    protected $routeRoot = 'nabre.manage.dash';

    function index()
    {
        $content = "Cruscotto manage";
        return view("Nabre::quick.manage", compact('content'));
    }
}
