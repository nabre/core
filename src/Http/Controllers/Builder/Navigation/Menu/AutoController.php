<?php

namespace Nabre\Http\Controllers\Builder\Navigation\Menu;

use Nabre\Models\Menu as Model;
use Nabre\Forms\Builder\Navigation\Menu\AutoForm as Form;
use Nabre\Tables\Builder\Navigation\Menu\AutoTable as Table;
use Nabre\Http\Controllers\Controller;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Validator;
use Nabre\Repositories\Menu\Generate;

class AutoController extends Controller
{

    protected $routeRoot = 'nabre.builder.navigation.menu.auto';

    function __construct()
    {
       $this->authorizeResource(Model::class,'data');
    }

    function index()
    {
        $content = (new Table())->html();
        return view("Nabre::quick.admin", compact('content'));
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
        return view('Nabre::quick.admin', compact('content'));
    }

    function update(Validator $validate, Model $data)
    {
        $validate->structure(new Form($data))->saveIn($data);
        return redirect()->route($this->getRoute('index'));
    }

    function destroy(Model $data)
    {
        $data->delete();
        return redirect()->route($this->getRoute('index'));
    }
}
