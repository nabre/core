<?php

namespace Nabre\Http\Controllers\Builder\Settings;

use Nabre\Models\Setting as Model;
use Nabre\Forms\Builder\Settings\VariablesForm as Form;
use Nabre\Tables\Builder\Settings\VariablesTable as Table;
use Nabre\Http\Controllers\Controller;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Validator;

class VariablesController extends Controller
{

    protected $routeRoot = 'nabre.builder.settings.variables';

    function __construct()
    {
       $this->authorizeResource(Model::class,'data');
    }

    function index()
    {
        $configKey = config('setting.override');
        collect($configKey)->each(function ($key) {
            $data = [config('setting.database.key') => $key];
            $set = Model::where(config('setting.database.key'), $key)->whereDoesntHave('user')->firstOrCreate();
            $set->recursiveSave($data);
        });
        Model::whereNotIn(config('setting.database.key'), $configKey)->delete();

        $content = (new Table())->html();
        return view("Nabre::quick.admin", compact('content'));
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
}
