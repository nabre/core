<?php

namespace Nabre\Http\Controllers\Admin\Users;

use Nabre\Tables\Admin\Users\ImpersonateTable as Table;
use Nabre\Http\Controllers\Controller;

class ImpersonateController extends Controller
{

    protected $routeRoot = 'nabre.admin.users.impersonate';

    function __construct()
    {
    }

    function index()
    {
        $content = (new Table())->html();
        return view("Nabre::quick.admin", compact('content'));
    }

    function edit($data)
    {
        \Auth::user()->setImpersonating($data);
        return redirect()->to('/');
    }

    function create()
    {
        \Auth::user()->stopImpersonating();
        return redirect()->route($this->getRoute('index'));
    }
}
