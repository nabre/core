<?php

namespace Nabre\Tables\Builder\Collections;

use Collective\Html\HtmlFacade as Html;
use Nabre\Repositories\Table\Structure;
use Nabre\Models\CollectionRelation as Model;

class RelationsTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['collection.class'];
    }

    function actions()
    {
        return [
            'create' => 'nabre.builder.collections.relations.create',
            'edit' => 'nabre.builder.collections.relations.edit',
            'destroy' => 'nabre.builder.collections.relations.destroy',
        ];
    }
/*
    function query()
    {
        return  $this->model::with('fields.coll')->get()->sortBy('class')->values();
    }*/
}
