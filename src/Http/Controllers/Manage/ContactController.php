<?php

namespace Nabre\Http\Controllers\Manage;

use App\Models\User;
use App\Models\UserContact as Model;
use Nabre\Forms\Manage\ContactForm as Form;
use Nabre\Tables\Manage\ContactTable as Table;
use Nabre\Http\Controllers\Controller;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Validator;
use Nabre\Services\UserContactService;

class ContactController extends Controller
{
    protected $routeRoot = 'nabre.manage.contact';

    function __construct()
    {
        $this->authorizeResource(Model::class, 'data');
    }

    function index()
    {
        if(config('setting.define.autousergenerate')){
            Model::doesnthave('user')->whereNotNull('email')->get()->each(function($data){
                UserContactService::generateUser($data);
            });
        }

        $content = (new Table())->html();
        return view("Nabre::quick.manage", compact('content'));
    }

    function create(Build $build)
    {
        return $this->edit($build, Model::make());
    }

    function store(Validator $validate)
    {
        return $this->update($validate, Model::make());
    }

    function edit(Build $build, Model $data)
    {
        $build = $build->structure(new Form($data));
        $content = $build->html($this->putUri($data), $data);
        return view('Nabre::quick.manage', compact('content'));
    }

    function update(Validator $validate, Model $data)
    {
        $validate->structure(new Form($data))->saveIn($data);
        return redirect()->route($this->getRoute('index'));
    }

    function userGenerate(Model $data)
    {
        UserContactService::generateUser($data);
        return redirect()->route($this->getRoute('index'));
    }

    function destroy(Model $data)
    {
        $data->delete();
        return redirect()->route($this->getRoute('index'));
    }
}
