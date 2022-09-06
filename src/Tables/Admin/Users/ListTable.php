<?php

namespace Nabre\Tables\Admin\Users;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Repositories\Table\Structure;
use App\Models\User as Model;
use Nabre\Repositories\Table\Columns;

class ListTable extends Structure
{

    var $model = Model::class;

    function columns()
    {
        return ['email', 'name','roles','contact','permissions'];
    }

    function colRoles()
    {
        $list = $this->col->map(function ($i) {
            $html = Html::tag(
                'li',
                $i->eti,
                ['class' => 'list-group-item p-1']
            );
            return compact('html');
        })->implode('html');
        return Html::tag('ul', $list, ['class' => 'list-group']);
    }

    function colContact(){
        return Columns::cast('boolean',!is_null($this->col));
    }

    function colPermissions()
    {
        $list = $this->col->map(function ($i) {
            $html = Html::tag(
                'li',
                $i->eti,
                ['class' => 'list-group-item p-1']
            );
            return compact('html');
        })->implode('html') ??null;
        return Html::tag('ul', $list, ['class' => 'list-group']);
    }

    function actions()
    {
        return [
            'create' => 'nabre.admin.users.list.create',
            'edit' => 'nabre.admin.users.list.edit',
            'destroy' => 'nabre.admin.users.list.destroy'
        ];
    }

    function query()
    {
        return $this->model::all()->sortBy('email')->values();
    }
}
