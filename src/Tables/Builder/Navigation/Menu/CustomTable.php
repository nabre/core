<?php

namespace Nabre\Tables\Builder\Navigation\Menu;

use Nabre\Repositories\Table\Structure;
use Nabre\Models\Menu as Model;

class CustomTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['name'];
    }

    function actions()
    {
        return [
            'create' => 'nabre.builder.navigation.menu.custom.create',
            'destroy' => 'nabre.builder.navigation.menu.custom.destroy',
            'edit' => 'nabre.builder.navigation.menu.custom.edit'
        ];
    }

    function query()
    {
        return $this->model::doesnthave('page')->get()->sortBy(['name'])->values();
    }

    function colItems(){
        return $this->col->pluck('page.string');
    }
}
