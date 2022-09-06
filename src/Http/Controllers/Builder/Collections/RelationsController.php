<?php

namespace Nabre\Http\Controllers\Builder\Collections;

use Nabre\Models\CollectionRelation as Model;
use Nabre\Forms\Builder\Collections\RelationsForm as Form;
use Nabre\Tables\Builder\Collections\RelationsTable as Table;
use Nabre\Http\Controllers\Controller;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Validator;
use Nabre\Services\CollectionService;

class RelationsController extends Controller
{
    protected $routeRoot = 'nabre.builder.collections.relations';

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
