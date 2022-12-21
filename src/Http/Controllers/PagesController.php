<?php

namespace Nabre\Http\Controllers;

use Nabre\Http\Controllers\Controller;

class PagesController extends Controller
{
    function welcome()
    {
        return view("Nabre::welcome");
    }
}
