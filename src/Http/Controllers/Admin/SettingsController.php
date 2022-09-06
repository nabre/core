<?php

namespace Nabre\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Nabre\Forms\Admin\SettingsForm as Form;
use Nabre\Http\Controllers\Controller;
use Nabre\Models\Setting as Model;
use Nabre\Repositories\Form\Build;

class SettingsController extends Controller
{

    protected $routeRoot = 'nabre.admin.settings';

    function __construct()
    {
       $this->authorizeResource(Model::class,'data');
    }

    function index(Build $build)
    {
        $build = $build->structure(Form::class)->boolParam('back', false);
        $content = $build->html($this->putUri());
        return view("Nabre::quick.admin", compact('content'));
    }

    function store(Request $request)
    {
        $requestValidateParam = collect(config('setting.override'))->map(function ($var) {
            $request = 'nullable';
            return get_defined_vars();
        })->pluck('request', 'var')->toArray();
        $vars = $request->validate($requestValidateParam);

        collect($vars)->each(function ($value, $key) {
            $data = [config('setting.database.key') => $key, config('setting.database.value') => $value];
            $set = Model::where(config('setting.database.key'), $key)->firstOrCreate();
            $set->recursiveSave($data);
        });

        return redirect()->route($this->getRoute('index'));
    }
}
