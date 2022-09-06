<?php

namespace Nabre\Tables\Builder\Navigation\Menu;

use Nabre\Repositories\Table\Structure;
use Nabre\Models\Menu as Model;

class AutoTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['name'];
    }

    function actions()
    {
        return [
            'create' => 'nabre.builder.navigation.menu.auto.create',
            'destroy' => 'nabre.builder.navigation.menu.auto.destroy',
            'edit' => 'nabre.builder.navigation.menu.auto.edit'
        ];
    }

    function query()
    {
        return $this->model::has('page')->get()->sortBy(['name'])->values();
    }
}
