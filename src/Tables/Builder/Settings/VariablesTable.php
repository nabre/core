<?php

namespace Nabre\Tables\Builder\Settings;

use Nabre\Repositories\Table\Structure;
use Nabre\Models\Setting as Model;

class VariablesTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return [config('setting.database.key'),'string','description','type','user_set'];
    }

    function actions()
    {
        return [
            'edit' => 'nabre.builder.settings.variables.edit'
        ];
    }

    function query()
    {
        return $this->model::whereDoesntHave('user')->get()->sortBy([config('setting.database.key')])->values();
    }
}
