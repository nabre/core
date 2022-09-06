<?php

namespace Nabre\Http\Controllers\Builder\Collections;

use Nabre\Models\Collection as Model;
use Nabre\Forms\Builder\Collections\FieldsForm as Form;
use Nabre\Tables\Builder\Collections\FieldsTable as Table;
use Nabre\Http\Controllers\Controller;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Validator;
use Nabre\Services\CollectionService;

class FieldsController extends Controller
{
    protected $routeRoot = 'nabre.builder.collections.fields';

    function __construct()
    {
       $this->authorizeResource(Model::class,'data');
    }

    function index()
    {
        CollectionService::checkExists();
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
        CollectionService::syncField($data);
        $build = $build->structure(new Form($data));
        $content = $build->html($this->putUri($data), $data);
        return view('Nabre::quick.admin', compact('content'));
    }

    function update(Validator $validate, Model $data)
    {
        $validate->structure(new Form($data))->saveIn($data);
        return redirect()->route($this->getRoute('index'));
    }
}
