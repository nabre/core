<?php

namespace Nabre\Http\Controllers\User\Profile;

use App\Models\User as Model;
use Nabre\Forms\User\Profile\UserForm as Form;
use Nabre\Http\Controllers\Controller;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Validator;

class AccountController extends Controller
{

    protected $routeRoot = 'nabre.user.profile.account';

    function __construct()
    {
    }

    function index(Build $build)
    {
        $data=\Auth::user();
        $build = $build->structure(new Form($data))->boolParam('back',false);
        $content = $build->html($this->putUri($data), $data);
        return view('Nabre::quick.user', compact('content'));
    }

    function update(Validator $validate, Model $data)
    {
        $validate->structure(new Form($data))->saveIn($data);
        return redirect()->route($this->getRoute('index'));
    }
}
