<?php

namespace Nabre\Http\Controllers\Builder;

use Nabre\Http\Controllers\Controller;
use Nabre\Repositories\Livewire;

class DatabaseController extends Controller
{
    protected $routeRoot = 'nabre.builder.database';

    function index()
    {
        $content = Livewire::load('databasedumprestore');
        return view("Nabre::quick.admin", compact('content'));
    }
}
