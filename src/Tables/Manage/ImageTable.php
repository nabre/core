<?php

namespace Nabre\Tables\Manage;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Repositories\Table\Structure;
use App\Models\Image as Model;
use Nabre\Services\Html\UserStatus;

class ImageTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['name'];
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
        })->implode('html');
        return Html::tag('ul', $list, ['class' => 'list-group']);
    }

    function actions()
    {
        return [
            'create' => 'nabre.manage.images.create',
            'edit' => 'nabre.manage.images.edit',
            'destroy' => 'nabre.manage.images.destroy',
        ];
    }

    function query()
    {
        return  $this->model::get()->sortBy('name')->values();
    }
}
