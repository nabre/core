<?php

namespace Nabre\Http\Controllers\User;

use Illuminate\Http\Request;
use Nabre\Forms\User\SettingsForm as Form;
use Nabre\Http\Controllers\Controller;
use Nabre\Models\Setting as Model;
use Nabre\Repositories\Form\Build;


class SettingsController extends Controller
{

    protected $routeRoot = 'nabre.user.settings';

    function __construct()
    {
    }

    function index(Build $build)
    {

        $build = $build->structure(Form::class)->boolParam('back', false);
        $content = $build->html($this->putUri());
        return view("Nabre::quick.user", compact('content'));
    }

    function store(Request $request)
    {

        $requestValidateParam = Model::where('user_set',true)->get()->pluck(config('setting.database.key'))->map(function ($var) {
            $request = 'nullable';
            return get_defined_vars();
        })->pluck('request', 'var')->toArray();
        $vars = $request->validate($requestValidateParam);

        collect($vars)->each(function ($value, $key) {
            $data = [config('setting.database.key') => $key, config('setting.database.value') => $value,'user'=>auth()->user()->id];
            $set = Model::where(config('setting.database.key'), $key)->whereHas('user',function($q){
                $q->where('_id',auth()->user()->id);
            })->firstOrCreate();
            $set->recursiveSave($data);
        });

        return redirect()->route($this->getRoute('index'));
    }
}
