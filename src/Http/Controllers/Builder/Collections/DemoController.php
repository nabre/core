<?php

namespace Nabre\Http\Controllers\Builder\Collections;

use Nabre\Http\Controllers\Controller;
use Nabre\Repositories\Livewire;

class DemoController extends Controller
{
    protected $routeRoot = 'nabre.builder.collections.relations';

    function index()
    {
        $content = Livewire::load('navigationconsole');
        return view("Nabre::quick.admin", compact('content'));
    }
}
