<?php

namespace Nabre\Http\Controllers\Builder\Settings;

use Nabre\Models\FormFieldType as Model;
use Nabre\Forms\Builder\Settings\FormFieldTypeForm as Form;
use Nabre\Tables\Builder\Settings\FormFieldTypeTable as Table;
use Nabre\Http\Controllers\Controller;
use Nabre\Models\Setting;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Validator;

class FormFieldTypeController extends Controller
{

    protected $routeRoot = 'nabre.builder.settings.form-field-type';

    function __construct()
    {
       $this->authorizeResource(Model::class,'data');
    }

    function index()
    {

        $configKey = Field::getConstants();
        collect($configKey)->each(function ($key) {
            $data = ['key' => $key];
            $set = Model::where('key', $key)->firstOrCreate();
            $set->recursiveSave($data);
        });
        Model::whereNotIn('key', $configKey)->delete();

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
