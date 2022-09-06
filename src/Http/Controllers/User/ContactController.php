<?php

namespace Nabre\Http\Controllers\User;

use App\Models\UserContact as Model;
use Illuminate\Http\Request;
use Nabre\Forms\User\ContactForm as Form;
use Nabre\Http\Controllers\Controller;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Validator;

class ContactController extends Controller
{

    protected $routeRoot = 'nabre.user.contact';

    function __construct()
    {
    }

    function index(Build $build)
    {
        $data=\Auth::user()->contact;
        if(is_null($data)){
            $data= new Model;
            $data=$data->recursiveSave(['user'=>\Auth::user()->id]);
        }

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
