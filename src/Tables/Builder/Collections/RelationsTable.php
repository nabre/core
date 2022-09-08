<?php

namespace Nabre\Tables\Builder\Collections;

use Collective\Html\HtmlFacade as Html;
use Nabre\Repositories\Table\Structure;
use Nabre\Models\Collection as Model;
use Nabre\Services\CollectionService;

class RelationsTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['class','filter','topFilter','parents','childs','system'];
    }

    function colFilter(){
        return $this->colSystem();
    }

    function colTopFilter(){
        return $this->colSystem();
    }

    function colParents(){
        return $this->colSystem();
    }

    function colChilds(){
        return $this->colSystem();
    }

    function colSystem()
    {
        $list = $this->col->map(function ($i) {
            $html = Html::tag(
                'li',
                CollectionService::getString(new $i->class)
                ,
                ['class' => 'list-group-item p-1']
            );
            return compact('html');
        })->implode('html');
        return $this->col->count().Html::tag('ul', $list, ['class' => 'list-group']);
    }

    function actions()
    {
        return [
          //  'create' => 'nabre.builder.collections.relations.create',
         //   'edit' => 'nabre.builder.collections.relations.edit',
          //  'destroy' => 'nabre.builder.collections.relations.destroy',
        ];
    }

    function query()
    {
        return  $this->model::get()->sortBy('class')->values();
    }
}
