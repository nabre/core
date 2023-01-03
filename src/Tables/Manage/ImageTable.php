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
        return ['name', 'preview'];
    }

    function colPreview()
    {
        $src = 'data:' . data_get($this->item, 'type') . ';base64,' . data_get($this->item->getRawOriginal(), 'code');
        return '<img src="' . $src . '" style="max-width:200px;max-height:200px" />';
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
