<?php

namespace Nabre\Tables\Builder\Collections;

use Collective\Html\HtmlFacade as Html;
use Nabre\Repositories\Table\Structure;
use Nabre\Models\Collection as Model;

class FieldsTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['class', 'string', 'fields'];
    }

    function colFields()
    {

        $list = $this->col->map(function ($i) {

            $html = Html::tag(
                'li',
                Html::div(
                    Html::div($i->name . ":", ['class' => 'col-5']) .
                        Html::div($i->string, ['class' => 'col']) .
                        Html::div($i->icon_type, ['class' => 'col-auto']),
                    ['class' => 'row']
                ),
                ['class' => 'list-group-item p-1']
            );

            return compact('html');
        })->implode('html');
        return Html::tag('ul', $list, ['class' => 'list-group']);
    }

    function actions()
    {
        return [
            'create' => 'nabre.builder.collections.fields.create',
            'edit' => 'nabre.builder.collections.fields.edit'
        ];
    }

    function query()
    {
        return  $this->model::with('fields.coll')->get()->sortBy('class')->values();
    }
}
